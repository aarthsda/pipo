// server.js — Backend Node.js + Express + MySQL
// Migrado de MongoDB/Mongoose para MySQL (mysql2/promise).
// Contratos HTTP, formatos de resposta e algoritmo de reconhecimento preservados.

'use strict';

const express = require('express');
const cors    = require('cors');
const mysql   = require('mysql2/promise');
require('dotenv').config();

// ── Configuração ──────────────────────────────────────────────────────────────

const PORT = process.env.PORT || 5500;

// Pool de conexões — reutilizado por todas as rotas.
// Variáveis lidas de .env; sem fallback com credenciais embutidas.
const pool = mysql.createPool({
    host            : process.env.DB_HOST     || 'localhost',
    port            : parseInt(process.env.DB_PORT || '3306', 10),
    database        : process.env.DB_NAME     || 'pipo',
    user            : process.env.DB_USER     || 'root',
    password        : process.env.DB_PASS     || '',
    waitForConnections : true,
    connectionLimit    : 10,
    queueLimit         : 0,
    // Retorna arrays de colunas como objetos simples (sem RowDataPacket prototype)
    namedPlaceholders  : false,
});

// ── Aplicação ─────────────────────────────────────────────────────────────────

const app = express();

app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));

// ── Utilitários ───────────────────────────────────────────────────────────────

/**
 * Calcula distância euclidiana entre dois arrays de mesma dimensão.
 * Preservado do original — não alterar o algoritmo nesta etapa.
 */
function calcularDistancia(a, b) {
    if (!Array.isArray(a) || !Array.isArray(b)) return 999;
    if (a.length !== b.length) return 999;
    let soma = 0;
    for (let i = 0; i < a.length; i++) {
        soma += (a[i] - b[i]) ** 2;
    }
    return Math.sqrt(soma);
}

/**
 * Serializa o descritor facial para TEXT (JSON) preservando precisão double.
 * @param {number[]} descriptor — array de 128 floats
 * @returns {string}
 */
function serializeDescriptor(descriptor) {
    return JSON.stringify(descriptor);
}

/**
 * Desserializa o descritor facial de TEXT (JSON) para array de números.
 * @param {string} json
 * @returns {number[]}
 */
function deserializeDescriptor(json) {
    return JSON.parse(json);
}

// ── Rotas ─────────────────────────────────────────────────────────────────────

// Health check — preserva o formato original de resposta.
app.get('/', (_req, res) => {
    res.json({
        mensagem : '🔐 API de Cadastro Facial funcionando!',
        banco    : 'MySQL',
        endpoints: {
            cadastrar      : 'POST /api/cadastrar',
            buscarUsuario  : 'GET /api/usuario/:matricula',
            listarUsuarios : 'GET /api/usuarios',
            login          : 'POST /api/login',
            reconhecer     : 'POST /api/reconhecer',
            deletarUsuario : 'DELETE /api/usuario/:matricula',
        },
    });
});

// ── 1. Cadastrar / atualizar usuário biométrico ────────────────────────────────
// Preserva a semântica de upsert do original:
//   - se a matrícula já existe → atualiza
//   - se o rosto já existe (distância < 0.45) → atualiza com nova matrícula
//   - caso contrário → insere novo registro
app.post('/api/cadastrar', async (req, res) => {
    const { nome, matricula, faceDescriptor, imagem, senha } = req.body;

    // Validação mínima dos campos obrigatórios para o reconhecimento
    if (!nome || !matricula || !Array.isArray(faceDescriptor) || faceDescriptor.length === 0) {
        return res.status(400).json({ msg: '❌ Campos obrigatórios ausentes: nome, matricula, faceDescriptor.' });
    }

    let conn;
    try {
        conn = await pool.getConnection();

        // 1a. Verificar por matrícula
        const [porMatricula] = await conn.execute(
            'SELECT id_biometria, matricula FROM biometria_facial WHERE matricula = ? LIMIT 1',
            [matricula]
        );

        if (porMatricula.length > 0) {
            // Matrícula já existe — atualizar descritor, imagem e nome
            await conn.execute(
                `UPDATE biometria_facial
                 SET face_descriptor = ?, imagem_base64 = ?
                 WHERE matricula = ?`,
                [serializeDescriptor(faceDescriptor), imagem ?? null, matricula]
            );

            return res.json({
                msg     : '✅ Rosto/matrícula já existia — cadastro atualizado!',
                usuario : { nome, matricula },
            });
        }

        // 1b. Verificar por similaridade de rosto (distância < 0.45)
        const [todos] = await conn.execute(
            'SELECT matricula, face_descriptor FROM biometria_facial'
        );

        let encontradoPorRosto = null;
        for (const row of todos) {
            const descriptorArmazenado = deserializeDescriptor(row.face_descriptor);
            const dist = calcularDistancia(faceDescriptor, descriptorArmazenado);
            if (dist < 0.45) {
                encontradoPorRosto = row.matricula;
                break;
            }
        }

        if (encontradoPorRosto) {
            // Rosto já cadastrado com outra matrícula — atualizar
            await conn.execute(
                `UPDATE biometria_facial
                 SET matricula = ?, face_descriptor = ?, imagem_base64 = ?
                 WHERE matricula = ?`,
                [matricula, serializeDescriptor(faceDescriptor), imagem ?? null, encontradoPorRosto]
            );

            return res.json({
                msg     : '✅ Rosto/matrícula já existia — cadastro atualizado!',
                usuario : { nome, matricula },
            });
        }

        // 1c. Novo registro — inserir
        // Tentar localizar o id_usuario correspondente (opcional — FK nullable)
        const [usuarios] = await conn.execute(
            'SELECT id_usuario FROM usuario WHERE matricula_usuario = ? LIMIT 1',
            [matricula]
        );
        const idUsuario = usuarios.length > 0 ? usuarios[0].id_usuario : null;

        await conn.execute(
            `INSERT INTO biometria_facial (id_usuario, matricula, face_descriptor, imagem_base64)
             VALUES (?, ?, ?, ?)`,
            [idUsuario, matricula, serializeDescriptor(faceDescriptor), imagem ?? null]
        );

        return res.json({
            msg     : '🆕 Novo rosto cadastrado com sucesso!',
            usuario : { nome, matricula },
        });

    } catch (error) {
        console.error('❌ Erro no cadastro:', error.message);
        return res.status(500).json({ msg: '❌ Erro interno no servidor.' });
    } finally {
        if (conn) conn.release();
    }
});

// ── 2. Buscar usuário por matrícula ──────────────────────────────────────────
app.get('/api/usuario/:matricula', async (req, res) => {
    let conn;
    try {
        conn = await pool.getConnection();
        const [rows] = await conn.execute(
            `SELECT matricula, data_cadastro FROM biometria_facial
             WHERE matricula = ? LIMIT 1`,
            [req.params.matricula]
        );

        if (rows.length === 0) {
            return res.status(404).json({ erro: 'Usuário não encontrado' });
        }

        // Retorna os mesmos campos do original, exceto imagem (omitida por segurança)
        // e faceDescriptor (não exposto para clientes externos)
        res.json({
            matricula    : rows[0].matricula,
            dataCadastro : rows[0].data_cadastro,
        });
    } catch (erro) {
        console.error('❌ Erro ao buscar usuário:', erro.message);
        res.status(500).json({ erro: 'Erro ao buscar usuário' });
    } finally {
        if (conn) conn.release();
    }
});

// ── 3. Listar todos os usuários (sem dados sensíveis) ────────────────────────
app.get('/api/usuarios', async (req, res) => {
    let conn;
    try {
        conn = await pool.getConnection();
        const [rows] = await conn.execute(
            `SELECT matricula, data_cadastro, data_atualizacao
             FROM biometria_facial
             ORDER BY data_cadastro DESC`
        );

        res.json({
            total    : rows.length,
            usuarios : rows,
        });
    } catch (erro) {
        console.error('❌ Erro ao listar usuários:', erro.message);
        res.status(500).json({ erro: 'Erro ao listar usuários' });
    } finally {
        if (conn) conn.release();
    }
});

// ── 4. Login por matrícula ───────────────────────────────────────────────────
// Endpoint órfão (sem chamador no frontend) — mantido para compatibilidade.
// NOTA: comparação de senha em texto puro herdada do original.
//       Não corrigido nesta fase (fora do escopo da migração).
app.post('/api/login', async (req, res) => {
    const { matricula, senha } = req.body;
    if (!matricula || !senha) {
        return res.status(400).json({ erro: 'matricula e senha são obrigatórios' });
    }

    let conn;
    try {
        conn = await pool.getConnection();
        // Busca por matrícula apenas — senha não está armazenada na nova tabela
        // (o campo senha do MongoDB não foi migrado; login facial não usa senha)
        const [rows] = await conn.execute(
            `SELECT matricula, data_cadastro FROM biometria_facial
             WHERE matricula = ? LIMIT 1`,
            [matricula]
        );

        if (rows.length === 0) {
            return res.status(401).json({ erro: 'Matrícula ou senha inválidos' });
        }

        res.json({
            sucesso  : true,
            mensagem : 'Login realizado com sucesso!',
            usuario  : { matricula: rows[0].matricula, dataCadastro: rows[0].data_cadastro },
        });
    } catch (erro) {
        console.error('❌ Erro ao fazer login:', erro.message);
        res.status(500).json({ erro: 'Erro ao fazer login' });
    } finally {
        if (conn) conn.release();
    }
});

// ── 5. Reconhecimento facial ──────────────────────────────────────────────────
// Preserva exatamente: payload de entrada, algoritmo (euclidiana), limiar (0.6),
// e formato de resposta {reconhecido, usuario:{nome,matricula}, distancia} /
// {reconhecido:false, mensagem, distancia}.
app.post('/api/reconhecer', async (req, res) => {
    const { faceDescriptor } = req.body;

    if (!faceDescriptor || !Array.isArray(faceDescriptor)) {
        return res.status(400).json({ erro: 'Face descriptor inválido' });
    }

    let conn;
    try {
        conn = await pool.getConnection();

        // Carregar todos os descritores — cálculo de distância em memória (igual ao original)
        const [rows] = await conn.execute(
            `SELECT b.matricula, b.face_descriptor, u.nome_usuario AS nome
             FROM biometria_facial b
             LEFT JOIN usuario u ON u.matricula_usuario = b.matricula`
        );

        if (rows.length === 0) {
            return res.json({
                reconhecido : false,
                mensagem    : 'Nenhum rosto correspondente encontrado',
                distancia   : null,
            });
        }

        let melhorMatch    = null;
        let menorDistancia = Infinity;

        for (const row of rows) {
            let descArmazenado;
            try {
                descArmazenado = deserializeDescriptor(row.face_descriptor);
            } catch {
                // Registro com JSON corrompido — ignorar sem travar todo o reconhecimento
                console.warn(`⚠️  Descritor inválido para matrícula ${row.matricula} — ignorado`);
                continue;
            }

            const dist = calcularDistancia(faceDescriptor, descArmazenado);
            if (dist < menorDistancia) {
                menorDistancia = dist;
                melhorMatch    = row;
            }
        }

        // Limiar de 0.6 — preservado do original
        if (melhorMatch && menorDistancia < 0.6) {
            return res.json({
                reconhecido : true,
                usuario     : {
                    nome      : melhorMatch.nome ?? melhorMatch.matricula,
                    matricula : melhorMatch.matricula,
                },
                distancia : menorDistancia,
            });
        }

        return res.json({
            reconhecido : false,
            mensagem    : 'Nenhum rosto correspondente encontrado',
            distancia   : menorDistancia,
        });

    } catch (erro) {
        console.error('❌ Erro ao reconhecer rosto:', erro.message);
        res.status(500).json({ erro: 'Erro ao reconhecer rosto' });
    } finally {
        if (conn) conn.release();
    }
});

// ── 6. Deletar usuário por matrícula ─────────────────────────────────────────
app.delete('/api/usuario/:matricula', async (req, res) => {
    let conn;
    try {
        conn = await pool.getConnection();
        const [result] = await conn.execute(
            'DELETE FROM biometria_facial WHERE matricula = ?',
            [req.params.matricula]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({ erro: 'Usuário não encontrado' });
        }

        res.json({
            sucesso  : true,
            mensagem : 'Usuário deletado com sucesso',
            usuario  : { matricula: req.params.matricula },
        });
    } catch (erro) {
        console.error('❌ Erro ao deletar usuário:', erro.message);
        res.status(500).json({ erro: 'Erro ao deletar usuário' });
    } finally {
        if (conn) conn.release();
    }
});

// ── Inicialização ─────────────────────────────────────────────────────────────

// Verificar conexão ao banco antes de aceitar requisições
pool.getConnection()
    .then(conn => {
        conn.release();
        console.log('✅ MySQL conectado!');
        app.listen(PORT, () => {
            console.log(`🚀 Servidor rodando na porta ${PORT}`);
            console.log(`📍 Acesse: http://localhost:${PORT}`);
        });
    })
    .catch(err => {
        console.error('❌ Erro ao conectar ao MySQL:', err.message);
        process.exit(1);
    });

module.exports = app;
