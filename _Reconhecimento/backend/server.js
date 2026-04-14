// server.js - Backend com Node.js + Express + MongoDB
// Instale as dependências: npm install express mongoose cors multer dotenv

const express = require('express');
require('dotenv').config();
const mongoose = require('mongoose');
const cors = require('cors');




const app = express();
const PORT = process.env.PORT || 5500;

// Middlewares
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));

// Conectar ao MongoDB
mongoose.connect(process.env.MONGODB_URI)
    .then(() => console.log('✅ MongoDB conectado!'))
    .catch(err => console.error('❌ Erro ao conectar MongoDB:', err));

console.log('MONGODB_URI:', process.env.MONGODB_URI);


// Schema do Usuário
const usuarioSchema = new mongoose.Schema({
    nome: {
        type: String,
        required: true,
        trim: true
    },
    matricula: {
        type: String,
        required: true,
        unique: true,
        trim: true
    },
    senha: {
        type: String,
        required: true
    },
    faceDescriptor: {
        type: [Number],
        required: true
    },
    imagem: {
        type: String, // Base64
        required: true
    },
    dataCadastro: {
        type: Date,
        default: Date.now
    }
});

const Usuario = mongoose.model('Usuario', usuarioSchema);

// ROTAS

// 1. Cadastrar novo usuário
app.post('/api/cadastrar', async (req, res) => {
    try {
        const { nome, matricula, faceDescriptor, imagem, senha } = req.body;

        let usuarioExistente = await Usuario.findOne({ matricula });

        if (!usuarioExistente) {
            const usuarios = await Usuario.find();
            for (let user of usuarios) {
                const distancia = calcularDistancia(faceDescriptor, user.faceDescriptor);
                if (distancia < 0.45) {
                    usuarioExistente = user;
                    break;
                }
            }
        }

        if (usuarioExistente) {
            usuarioExistente.nome = nome;
            usuarioExistente.matricula = matricula;
            usuarioExistente.faceDescriptor = faceDescriptor;
            usuarioExistente.imagem = imagem;
            await usuarioExistente.save();

            return res.json({
                msg: '✅ Rosto/matrícula já existia — cadastro atualizado!',
                usuario: usuarioExistente
            });
        }

        const novoUsuario = new Usuario({ nome, matricula, senha, faceDescriptor, imagem });
        await novoUsuario.save();

        return res.json({ msg: '🆕 Novo rosto cadastrado com sucesso!', usuario: novoUsuario });

    } catch (error) {
        console.error('❌ Erro no cadastro:', error);
        return res.status(500).json({
            msg: '❌ Erro interno no servidor.',
            detalhes: error.message
        });
    }
});


function calcularDistancia(a, b) {
    if (!a || !b) return 999;
    let soma = 0;
    for (let i = 0; i < a.length; i++) {
        soma += (a[i] - b[i]) ** 2;
    }
    return Math.sqrt(soma);
}

// 2. Buscar usuário por matrícula
app.get('/api/usuario/:matricula', async (req, res) => {
    try {
        const usuario = await Usuario.findOne({
            matricula: req.params.matricula
        });

        if (!usuario) {
            return res.status(404).json({
                erro: 'Usuário não encontrado'
            });
        }

        res.json({
            nome: usuario.nome,
            matricula: usuario.matricula,
            faceDescriptor: usuario.faceDescriptor,
            imagem: usuario.imagem,
            dataCadastro: usuario.dataCadastro
        });
    } catch (erro) {
        res.status(500).json({
            erro: 'Erro ao buscar usuário',
            detalhes: erro.message
        });
    }
});

// 3. Listar todos os usuários (sem senha)
app.get('/api/usuarios', async (req, res) => {
    try {
        const usuarios = await Usuario.find()
            .select('-senha -imagem') // Não retorna senha e imagem por segurança
            .sort({ dataCadastro: -1 });

        res.json({
            total: usuarios.length,
            usuarios
        });
    } catch (erro) {
        res.status(500).json({
            erro: 'Erro ao listar usuários',
            detalhes: erro.message
        });
    }
});

// 4. Verificar login (matrícula + senha)
app.post('/api/login', async (req, res) => {
    try {
        const { matricula, senha } = req.body;

        const usuario = await Usuario.findOne({ matricula, senha });

        if (!usuario) {
            return res.status(401).json({
                erro: 'Matrícula ou senha inválidos'
            });
        }

        res.json({
            sucesso: true,
            mensagem: 'Login realizado com sucesso!',
            usuario: {
                nome: usuario.nome,
                matricula: usuario.matricula,
                dataCadastro: usuario.dataCadastro
            }
        });
    } catch (erro) {
        res.status(500).json({
            erro: 'Erro ao fazer login',
            detalhes: erro.message
        });
    }
});

// 5. Reconhecimento facial
app.post('/api/reconhecer', async (req, res) => {
    try {
        const { faceDescriptor } = req.body;

        if (!faceDescriptor || !Array.isArray(faceDescriptor)) {
            return res.status(400).json({
                erro: 'Face descriptor inválido'
            });
        }

        // Buscar todos os usuários
        const usuarios = await Usuario.find();

        // Calcular distância euclidiana
        let melhorMatch = null;
        let menorDistancia = Infinity;

        for (const usuario of usuarios) {
            const distancia = calcularDistanciaEuclidiana(
                faceDescriptor,
                usuario.faceDescriptor
            );

            if (distancia < menorDistancia) {
                menorDistancia = distancia;
                melhorMatch = usuario;
            }
        }

        // Threshold de 0.6 (ajuste conforme necessário)
        if (menorDistancia < 0.6) {
            res.json({
                reconhecido: true,
                usuario: {
                    nome: melhorMatch.nome,
                    matricula: melhorMatch.matricula
                },
                distancia: menorDistancia
            });
        } else {
            res.json({
                reconhecido: false,
                mensagem: 'Nenhum rosto correspondente encontrado',
                distancia: menorDistancia
            });
        }
    } catch (erro) {
        res.status(500).json({
            erro: 'Erro ao reconhecer rosto',
            detalhes: erro.message
        });
    }
});

// 6. Deletar usuário
app.delete('/api/usuario/:matricula', async (req, res) => {
    try {
        const usuario = await Usuario.findOneAndDelete({
            matricula: req.params.matricula
        });

        if (!usuario) {
            return res.status(404).json({
                erro: 'Usuário não encontrado'
            });
        }

        res.json({
            sucesso: true,
            mensagem: 'Usuário deletado com sucesso',
            usuario: {
                nome: usuario.nome,
                matricula: usuario.matricula
            }
        });
    } catch (erro) {
        res.status(500).json({
            erro: 'Erro ao deletar usuário',
            detalhes: erro.message
        });
    }
});

// Função auxiliar: Calcular distância euclidiana
function calcularDistanciaEuclidiana(arr1, arr2) {
    if (arr1.length !== arr2.length) {
        throw new Error('Arrays devem ter o mesmo tamanho');
    }

    let soma = 0;
    for (let i = 0; i < arr1.length; i++) {
        soma += Math.pow(arr1[i] - arr2[i], 2);
    }

    return Math.sqrt(soma);
}

// Rota de teste
app.get('/', (req, res) => {
    res.json({
        mensagem: '🔐 API de Cadastro Facial funcionando!',
        endpoints: {
            cadastrar: 'POST /api/cadastrar',
            buscarUsuario: 'GET /api/usuario/:matricula',
            listarUsuarios: 'GET /api/usuarios',
            login: 'POST /api/login',
            reconhecer: 'POST /api/reconhecer',
            deletarUsuario: 'DELETE /api/usuario/:matricula'
        }
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`🚀 Servidor rodando na porta ${PORT}`);
    console.log(`📍 Acesse: http://localhost:${PORT}`);
});

module.exports = app;