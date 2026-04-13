# 🚀 Como Usar o Sistema de Pedidos de Lanches no XAMPP

## 📋 Pré-requisitos

- **XAMPP** instalado (versão 8.0 ou superior recomendada)
- **Navegador web** (Chrome, Firefox, Edge, etc.)
- **Editor de texto** (VS Code, Notepad++, etc.)

## 📂 Passo 1: Preparar o XAMPP

### 1.1 Instalar e Iniciar o XAMPP
1. Baixe o XAMPP do site oficial: https://www.apachefriends.org/
2. Instale o XAMPP no diretório padrão (geralmente `C:\xampp\`)
3. Abra o **XAMPP Control Panel**
4. Inicie os módulos **Apache** e **MySQL** clicando em "Start"

### 1.2 Verificar se está funcionando
1. Abra o navegador
2. Acesse: `http://localhost/`
3. Você deve ver a página inicial do XAMPP

## 📂 Passo 2: Configurar o Sistema

### 2.1 Copiar os arquivos do sistema
1. Localize a pasta do projeto `sistema-lanches`
2. Copie a pasta inteira para: `C:\xampp\htdocs\`
3. A estrutura final deve ficar: `C:\xampp\htdocs\sistema-lanches\`

### 2.2 Criar o banco de dados
1. Abra o navegador e acesse: `http://localhost/phpmyadmin/`
2. Clique em **"Novo"** (ou **"New"**) no menu lateral esquerdo
3. **Nome do banco de dados:** `sistema_lanches`
4. **Collation:** `utf8_general_ci`
5. Clique em **"Criar"**

### 2.3 Executar o script SQL
1. No phpMyAdmin, clique no banco `sistema_lanches` que você criou
2. Clique na aba **"SQL"** no topo
3. Abra o arquivo `database.sql` que está na pasta do projeto
4. Copie todo o conteúdo do arquivo
5. Cole no campo SQL do phpMyAdmin
6. Clique em **"Executar"**

## 🔧 Passo 3: Configurar a Conexão (se necessário)

### 3.1 Verificar configurações do banco
Por padrão, o XAMPP usa:
- **Host:** `localhost`
- **Usuário:** `root`
- **Senha:** *(vazia)*

### 3.2 Ajustar configurações (se necessário)
Se suas configurações do MySQL forem diferentes, edite o arquivo:
```
C:\xampp\htdocs\sistema-lanches\config\database.php
```

Procure estas linhas e ajuste conforme necessário:
```php
private $host = 'localhost';
private $db_name = 'sistema_lanches';
private $username = 'root';
private $password = '';
```

## 🌐 Passo 4: Acessar o Sistema

### 4.1 URL do Sistema
Abra o navegador e acesse:
```
http://localhost/sistema-lanches/
```

### 4.2 Contas de Teste

#### Administrador
- **URL:** `http://localhost/sistema-lanches/admin/dashboard.php`
- **Email:** `admin@sistema.com`
- **Senha:** `admin123`

#### Cliente
- **Email:** `cliente@sistema.com`
- **Senha:** `cliente123`

## 🔍 Passo 5: Verificar se Está Funcionando

### 5.1 Teste Básico
1. Acesse a URL principal
2. Deve aparecer a página inicial com produtos
3. Tente fazer login com as contas de teste

### 5.2 Teste do Painel Admin
1. Acesse `http://localhost/sistema-lanches/admin/dashboard.php`
2. Faça login com `admin@sistema.com` / `admin123`
3. Deve aparecer o dashboard com estatísticas

### 5.3 Teste de Funcionalidades
1. **Como Cliente:**
   - Navegue pelo cardápio
   - Adicione produtos ao carrinho
   - Faça um pedido
   - Verifique o histórico

2. **Como Admin:**
   - Visualize pedidos
   - Altere status dos pedidos
   - Adicione/edite produtos
   - Gerencie categorias e usuários

## 🐛 Solução de Problemas

### Problema: "Página não carrega" ou "Erro 404"
**Solução:**
- Verifique se Apache está rodando no XAMPP Control Panel
- Confirme se a pasta está em `C:\xampp\htdocs\sistema-lanches\`
- Tente acessar diretamente: `http://localhost/sistema-lanches/index.php`

### Problema: "Erro de conexão com banco de dados"
**Solução:**
- Verifique se MySQL está rodando no XAMPP Control Panel
- Confirme se o banco `sistema_lanches` foi criado
- Verifique as credenciais em `config/database.php`
- Teste a conexão acessando `http://localhost/phpmyadmin/`

### Problema: "Permissões de pasta" (para upload de imagens)
**Solução:**
1. Vá para a pasta: `C:\xampp\htdocs\sistema-lanches\assets\images\produtos\`
2. Clique com botão direito → Propriedades
3. Na aba "Segurança" → Editar
4. Dê permissões completas para o usuário atual

### Problema: "PHP não encontra arquivos"
**Solução:**
- Verifique se todos os arquivos foram copiados corretamente
- Confirme se não há arquivos corrompidos
- Tente recarregar a página com Ctrl+F5

## 📱 Teste em Dispositivos Móveis

Para testar a responsividade:
1. No Chrome, pressione F12
2. Clique no ícone de dispositivo móvel (telefone/tablet)
3. Escolha diferentes dispositivos para testar

## 🔄 Próximos Passos

Após configurar com sucesso:

1. **Personalize o sistema:**
   - Edite cores e estilos em `assets/css/style.css`
   - Altere textos e configurações em `config/config.php`
   - Adicione seu logotipo nas imagens

2. **Adicione produtos:**
   - Acesse o painel admin
   - Vá em "Gerenciar Produtos"
   - Adicione seus produtos com fotos

3. **Configure usuários:**
   - Crie contas para funcionários
   - Configure diferentes níveis de acesso

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs do Apache em `C:\xampp\apache\logs\error.log`
2. Verifique os logs do PHP em `C:\xampp\php\logs\php_error_log`
3. Consulte a documentação em `README.md`

---

**🎉 Sistema pronto para uso! Aproveite seu sistema de pedidos de lanches!**