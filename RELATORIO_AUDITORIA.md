# Relatório de verificação técnica do projeto PIPO

Data da análise: 2026-04-14 (UTC)

## 1) Testes e checks executados

### 1.1 Ambientes
- `php -v` → PHP 8.5.3-dev disponível.
- `node -v` → Node.js v22.21.1 disponível.

### 1.2 Testes automatizados Node
- `npm --prefix _Reconhecimento test` → **falhou** porque o projeto não possui script `test`.
- `npm --prefix _Reconhecimento/backend test` → **falhou** com `Error: no test specified` (script existe, mas está como placeholder e sempre retorna erro).

### 1.3 Verificação de sintaxe PHP
- Rodado lint em todos os arquivos `*.php` (excluindo `node_modules`):
  - `find . -type f -name '*.php' -not -path './_Reconhecimento/node_modules/*' -not -path './_Reconhecimento/backend/node_modules/*' -print0 | xargs -0 -n1 php -l`
- Resultado: **sem erros de sintaxe** nos arquivos verificados.

## 2) Problemas encontrados (prioridade alta)

1. **Autenticação baseada em senha em cookie (texto puro)**
   - O login grava `user_password` diretamente em cookie com a senha digitada.
   - Isso é um risco crítico de segurança (exposição via navegador, extensões, logs, interceptação em ambiente sem HTTPS, etc.).
   - Impacto: comprometimento de contas e escalonamento de acesso.

2. **Validação de sessão depende dessa senha em cookie**
   - A checagem de sessão usa `verifyPassword($_COOKIE['user_password'])`.
   - Mesmo com hash no banco, o transporte/armazenamento da senha em cookie é inseguro.

3. **Sem suíte de testes real**
   - Projeto principal e backend de reconhecimento não têm testes automatizados úteis para regressão.
   - Isso aumenta risco de quebrar funcionalidades ao alterar código.

## 3) Redundâncias e pontos de sobrecarga

1. **Código duplicado entre `pages` e `pages-old`**
   - Foram encontrados **17 arquivos PHP idênticos** entre as duas pastas (mesmo hash de conteúdo).
   - Isso aumenta esforço de manutenção e risco de inconsistência.

2. **Grande volume versionado de dependências (`node_modules`)**
   - O repositório possui diretórios `node_modules` commitados.
   - Tamanho observado:
     - `_Reconhecimento/node_modules`: ~21M
     - `_Reconhecimento/backend/node_modules`: ~22M
   - Resultado: repositório mais pesado, diffs ruidosos, conflitos e alterações acidentais em dependências.

3. **Ausência de `.gitignore` no repositório**
   - Facilita commit acidental de artefatos e dependências geradas.

4. **Repetição de padrão CRUD em vários models**
   - `models/admin.php`, `models/usuario.php`, `models/reserva.php`, etc. repetem quase a mesma lógica.
   - Isso eleva custo de manutenção e chance de bugs por divergência.

## 4) Recomendações objetivas (o que resolver primeiro)

### Fase 1 — Segurança e base do fluxo (urgente)
1. **Remover senha de cookies imediatamente**.
   - Substituir por sessão no servidor (`$_SESSION`) + ID de usuário e papel.
   - Cookies de sessão com `HttpOnly`, `Secure`, `SameSite=Lax/Strict`.
2. **Rotacionar sessão no login** (`session_regenerate_id(true)`).
3. **Padronizar middleware de autenticação** (um arquivo único para validar sessão).

### Fase 2 — Qualidade e manutenção
4. **Criar `.gitignore`** para excluir `node_modules`, uploads temporários, logs e arquivos de build.
5. **Parar de versionar dependências instaladas**.
   - Manter apenas `package.json` e `package-lock.json`.
6. **Remover/arquivar `pages-old`** fora da árvore principal (ou migrar para branch/tag de histórico).
7. **Extrair uma classe base de Model/Repository** para reduzir duplicação de CRUD.

### Fase 3 — Testes e melhoria de arquitetura
8. **Adicionar testes mínimos obrigatórios**:
   - backend Node: autenticação/rotas críticas;
   - PHP: testes de integração de login e fluxos principais.
9. **Adicionar pipeline de CI** (lint + testes em push/PR).
10. **Separar frontend e backend com API REST clara** (se evoluir o projeto), mantendo sessão/token seguro.

## 5) Tecnologias que podem substituir abordagens atuais

- **Sessão servidor (PHP nativo)** no lugar de senha em cookie.
- **Monolog** (ou logging equivalente) no lugar de logs improvisados.
- **PHPCS/PHPStan + ESLint** para qualidade estática.
- **Jest/Vitest (Node) + PHPUnit/Pest (PHP)** para cobertura automatizada.
- **Docker Compose** para padronizar ambiente de desenvolvimento.

## 6) Conclusão direta

O projeto funciona em estrutura MVC simples e sem erros sintáticos de PHP, porém há um risco de segurança grave na autenticação por cookie com senha em texto puro, além de alta duplicação de código e repositório inchado por dependências versionadas. Corrigir autenticação/sessão e saneamento do repositório deve ser a prioridade número 1.
