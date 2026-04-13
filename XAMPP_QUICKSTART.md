# 🚀 Instalação Rápida - XAMPP

## ⚡ Passos Rápidos

### 1. Preparar XAMPP
- Instale XAMPP (se não tiver)
- Abra XAMPP Control Panel
- Clique "Start" em Apache e MySQL

### 2. Configurar Sistema
```bash
# Copie a pasta sistema-lanches para:
# C:\xampp\htdocs\sistema-lanches\
```

### 3. Criar Banco de Dados
1. Acesse: `http://localhost/phpmyadmin/`
2. Clique "Novo" → Nome: `sistema_lanches`
3. Clique "SQL" → Cole o conteúdo de `database.sql`
4. Clique "Executar"

### 4. Verificar
Acesse: `http://localhost/sistema-lanches/check_install.php`

### 5. Usar Sistema
- **Site:** `http://localhost/sistema-lanches/`
- **Admin:** `http://localhost/sistema-lanches/admin/dashboard.php`
- **Login Admin:** `admin@sistema.com` / `admin123`

## 📋 Checklist de Verificação

- [ ] Apache rodando (verde no XAMPP)
- [ ] MySQL rodando (verde no XAMPP)
- [ ] Pasta copiada para `htdocs`
- [ ] Banco criado no phpMyAdmin
- [ ] Script SQL executado
- [ ] Página inicial carrega
- [ ] Login funciona

## 🆘 Problemas Comuns

| Problema | Solução |
|----------|---------|
| 404 Error | Verificar se pasta está em `htdocs` |
| DB Error | Verificar MySQL rodando e banco criado |
| Upload Error | Dar permissões na pasta `images/produtos` |
| PHP Error | Verificar versão PHP 8.0+ |

## 📞 Precisa de Ajuda?

1. Execute: `http://localhost/sistema-lanches/check_install.php`
2. Leia: `XAMPP_SETUP.md` (instruções completas)
3. Verifique logs em: `C:\xampp\apache\logs\error.log`

---
**⏱️ Tempo estimado: 10-15 minutos**