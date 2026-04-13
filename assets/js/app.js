// Sistema de Pedidos de Lanches - JavaScript Principal

// Carrinho de compras
class Carrinho {
    constructor() {
        this.itens = JSON.parse(localStorage.getItem('carrinho')) || [];
        this.atualizarContador();
    }

    adicionarItem(produto, quantidade = 1, observacoes = '') {
        const itemExistente = this.itens.find(item =>
            item.produto_id === produto.id &&
            item.observacoes === observacoes
        );

        if (itemExistente) {
            itemExistente.quantidade += quantidade;
        } else {
            this.itens.push({
                produto_id: produto.id,
                nome: produto.nome,
                preco: produto.preco,
                quantidade: quantidade,
                observacoes: observacoes,
                imagem: produto.imagem
            });
        }

        this.salvar();
        this.atualizarContador();
        this.mostrarNotificacao(`${produto.nome} adicionado ao carrinho!`, 'success');
    }

    removerItem(index) {
        const item = this.itens[index];
        this.itens.splice(index, 1);
        this.salvar();
        this.atualizarContador();
        this.mostrarNotificacao(`${item.nome} removido do carrinho!`, 'info');
    }

    alterarQuantidade(index, novaQuantidade) {
        if (novaQuantidade <= 0) {
            this.removerItem(index);
            return;
        }

        this.itens[index].quantidade = novaQuantidade;
        this.salvar();
        this.atualizarContador();
    }

    calcularTotal() {
        return this.itens.reduce((total, item) => total + (item.preco * item.quantidade), 0);
    }

    getQuantidadeTotal() {
        return this.itens.reduce((total, item) => total + item.quantidade, 0);
    }

    limpar() {
        this.itens = [];
        this.salvar();
        this.atualizarContador();
    }

    salvar() {
        localStorage.setItem('carrinho', JSON.stringify(this.itens));
    }

    atualizarContador() {
        const contador = document.querySelector('.cart-count');
        const quantidade = this.getQuantidadeTotal();

        if (contador) {
            if (quantidade > 0) {
                contador.textContent = quantidade > 99 ? '99+' : quantidade;
                contador.style.display = 'flex';
            } else {
                contador.style.display = 'none';
            }
        }
    }

    mostrarNotificacao(mensagem, tipo = 'info') {
        // Criar toast de notificação
        const toastHtml = `
            <div class="toast-custom">
                <div class="toast align-items-center text-white bg-${tipo} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${mensagem}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.querySelector('.toast-custom .toast');
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        // Remover do DOM após ocultar
        toastElement.addEventListener('hidden.bs.toast', () => {
            document.querySelector('.toast-custom').remove();
        });
    }
}

// Instância global do carrinho
const carrinho = new Carrinho();

// Funções utilitárias
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

function mostrarLoading(elemento, mostrar = true) {
    if (mostrar) {
        elemento.innerHTML = '<div class="text-center"><div class="spinner"></div> Carregando...</div>';
    }
}

function validarFormulario(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valido = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            valido = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return valido;
}

// Event listeners globais
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Máscara para telefone
    const telefoneInputs = document.querySelectorAll('input[name="telefone"]');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 2) {
                    value = value;
                } else if (value.length <= 6) {
                    value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
                } else if (value.length <= 10) {
                    value = `(${value.slice(0, 2)}) ${value.slice(2, 6)}-${value.slice(6)}`;
                } else {
                    value = `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
                }
                e.target.value = value;
            }
        });
    });

    // Validação de formulários
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validarFormulario(form)) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    });

    // Animações de fade-in
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card-food').forEach(card => {
        observer.observe(card);
    });
});

// Função para adicionar ao carrinho (usada nos botões)
function appAdicionarAoCarrinho(produtoId, nome, preco, imagem = null) {
    const produto = {
        id: produtoId,
        nome: nome,
        preco: parseFloat(preco),
        imagem: imagem
    };

    carrinho.adicionarItem(produto);
}

// Função para remover do carrinho
function appRemoverDoCarrinho(index) {
    carrinho.removerItem(index);
}

// Função para alterar quantidade
function appAlterarQuantidade(index, novaQuantidade) {
    carrinho.alterarQuantidade(index, novaQuantidade);
}

// Função de fallback para páginas que não implementam checkout próprio.
function appFinalizarPedidoFallback() {
    if (carrinho.itens.length === 0) {
        alert('Adicione itens ao carrinho antes de finalizar o pedido.');
        return;
    }

    window.location.href = '/sistema-lanches/cliente/carrinho.php';
}

// Exportar carrinho para uso global
window.Carrinho = Carrinho;
window.carrinho = carrinho;
window.formatarMoeda = formatarMoeda;
if (typeof window.adicionarAoCarrinho !== 'function') {
    window.adicionarAoCarrinho = appAdicionarAoCarrinho;
}
if (typeof window.removerDoCarrinho !== 'function') {
    window.removerDoCarrinho = appRemoverDoCarrinho;
}
if (typeof window.alterarQuantidade !== 'function') {
    window.alterarQuantidade = appAlterarQuantidade;
}
if (typeof window.finalizarPedido !== 'function') {
    window.finalizarPedido = appFinalizarPedidoFallback;
}