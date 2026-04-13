# Sistema de Pedidos de Lanches 🍔

Um sistema completo de pedidos de lanches desenvolvido em PHP com MySQL, incluindo painel administrativo e interface responsiva para clientes.

## � Documentação e Instalação

### Arquivos de Ajuda
- [`XAMPP_SETUP.md`](XAMPP_SETUP.md) - Guia completo para XAMPP (recomendado)
- [`XAMPP_QUICKSTART.md`](XAMPP_QUICKSTART.md) - Instalação rápida
- [`check_install.php`](check_install.php) - Verificação automática da instalação
- [`iniciar_sistema.bat`](iniciar_sistema.bat) - Menu de atalhos para Windows
- [`README.md`](README.md) - Documentação completa (este arquivo)

### Instalação Rápida (XAMPP)
```bash
# 1. Copie a pasta sistema-lanches para C:\xampp\htdocs\
# 2. Execute iniciar_sistema.bat como administrador
# 3. Siga as instruções no menu
```

## �🚀 Funcionalidades

### Para Clientes
- ✅ Cadastro e login de usuários
- ✅ Visualização do cardápio com categorias
- ✅ Carrinho de compras com localStorage
- ✅ Sistema de pedidos com acompanhamento de status
- ✅ Histórico de pedidos
- ✅ Interface responsiva (mobile-friendly)

### Para Administradores
- ✅ Dashboard com estatísticas em tempo real
- ✅ Gerenciamento completo de produtos (CRUD)
- ✅ Gerenciamento de categorias
- ✅ Controle de pedidos (status, detalhes)
- ✅ Gerenciamento de usuários
- ✅ Relatórios e métricas

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8+ com PDO
- **Banco de Dados**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5
- **Ícones**: Font Awesome
- **Arquitetura**: MVC-like com classes organizadas

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx) ou XAMPP
- Composer (opcional, para dependências futuras)

## 🚀 Instalação

### Opção 1: Instalação Rápida com XAMPP (Recomendado)

Se você usa XAMPP, siga o guia completo em [`XAMPP_SETUP.md`](XAMPP_SETUP.md) ou:

1. **Instale e inicie o XAMPP** (Apache + MySQL)
2. **Copie a pasta** `sistema-lanches` para `C:\xampp\htdocs\`
3. **Crie o banco de dados:**
   - Acesse `http://localhost/phpmyadmin/`
   - Crie banco `sistema_lanches`
   - Execute o script `database.sql`
4. **Verifique a instalação:** `http://localhost/sistema-lanches/check_install.php`
5. **Acesse o sistema:** `http://localhost/sistema-lanches/`

### Opção 2: Instalação Manual

#### 1. Clone ou baixe o projeto
```bash
# Se estiver usando Git
git clone https://github.com/seu-usuario/sistema-lanches.git

# Ou baixe o ZIP e extraia para seu servidor web
```

#### 2. Configure o banco de dados
```sql
-- Execute o arquivo database.sql no seu MySQL
-- Este arquivo contém toda a estrutura das tabelas e dados de exemplo
```

### 3. Configure a conexão com o banco
Edite o arquivo `config/database.php` e ajuste as credenciais:
```php
private $host = 'localhost';
private $db_name = 'sistema_lanches';
private $username = 'root';
private $password = '';
```

### 4. Configure as permissões
Certifique-se de que o diretório `assets/images/produtos/` tenha permissões de escrita:
```bash
chmod 755 assets/images/produtos/
```

### 5. Acesse o sistema
- **URL do sistema**: `http://localhost/sistema-lanches/`
- **Painel Admin**: `http://localhost/sistema-lanches/admin/dashboard.php`

## 👤 Contas de Teste

### Administrador
- **Email**: admin@sistema.com
- **Senha**: admin123

### Cliente de Exemplo
- **Email**: cliente@sistema.com
- **Senha**: cliente123

## 📁 Estrutura do Projeto

```
sistema-lanches/
├── admin/                    # Painel administrativo
│   ├── api/                 # APIs do admin
│   ├── dashboard.php        # Dashboard principal
│   ├── pedidos.php          # Gerenciar pedidos
│   ├── produtos.php         # Gerenciar produtos
│   ├── categorias.php       # Gerenciar categorias
│   └── usuarios.php         # Gerenciar usuários
├── assets/                  # Recursos estáticos
│   ├── css/
│   │   └── style.css       # Estilos customizados
│   ├── js/
│   │   └── app.js          # JavaScript do frontend
│   └── images/
│       └── produtos/        # Imagens dos produtos
├── classes/                 # Classes PHP (MVC)
│   ├── Usuario.php         # Gerenciamento de usuários
│   ├── Produto.php         # Gerenciamento de produtos
│   ├── Pedido.php          # Gerenciamento de pedidos
│   └── Categoria.php       # Gerenciamento de categorias
├── cliente/                 # Área do cliente
│   ├── menu.php            # Cardápio
│   ├── carrinho.php        # Carrinho de compras
│   ├── pedidos.php         # Histórico de pedidos
│   └── api/                # APIs do cliente
├── config/                  # Configurações
│   └── database.php        # Conexão com banco de dados
├── templates/               # Templates reutilizáveis
│   ├── header.php          # Cabeçalho do site
│   └── footer.php          # Rodapé do site
├── database.sql            # Script do banco de dados
├── index.php               # Página inicial
├── login.php               # Página de login
├── cadastro.php            # Página de cadastro
└── logout.php              # Logout do sistema
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais
- `usuarios` - Dados dos usuários (clientes, funcionários, admins)
- `categorias` - Categorias dos produtos
- `produtos` - Catálogo de produtos
- `pedidos` - Pedidos realizados
- `itens_pedido` - Itens de cada pedido

### Status dos Pedidos
- `pendente` - Aguardando confirmação
- `preparando` - Em preparação
- `pronto` - Pronto para retirada/entrega
- `entregue` - Finalizado
- `cancelado` - Cancelado

## 🔧 Configurações Avançadas

### Upload de Imagens
- **Diretório**: `assets/images/produtos/`
- **Formatos aceitos**: JPG, PNG, GIF
- **Tamanho máximo**: 2MB
- **Redimensionamento**: Automático (recomendado implementar)

### Sessões PHP
- **Tempo de vida**: 24 horas (configurável em `php.ini`)
- **Segurança**: Sessões HTTP-only habilitadas



## 🐛 Troubleshooting

### Problemas Comuns no XAMPP




### Verificação de Instalação
Execute o script de verificação:
```
http://localhost/sistema-lanches/check_install.php
```

Este script verifica automaticamente:
- Versão do PHP
- Extensões necessárias
- Conexão com banco
- Arquivos e pastas
  Permissões
# 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request
