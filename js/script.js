// JavaScript Personalizado - Biblioteca Online

// Auto-hide alerts após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
    
    // Confirmar antes de eliminar
    const deleteButtons = document.querySelectorAll('.btn-danger[href*="eliminar"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja eliminar este item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Validação de formulários
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Smooth scroll para links âncora
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Adicionar animação de loading aos botões de submit
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>A processar...';
            
            // Restaurar botão após 3 segundos (caso não haja redirect)
            setTimeout(function() {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 3000);
        });
    });
    
    // Tooltip initialization (Bootstrap 5)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Pesquisa em tempo real (opcional)
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Auto-submit após 500ms de inatividade
                if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                    searchInput.form.submit();
                }
            }, 500);
        });
    }
    
    // Confirmar devolução de livros
    const devolverButtons = document.querySelectorAll('a[href*="devolver"]');
    devolverButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Confirma a devolução deste livro?')) {
                e.preventDefault();
            }
        });
    });
    
    // Adicionar classe active ao link atual no navbar
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(function(link) {
        if (link.getAttribute('href') === currentLocation.split('/').pop()) {
            link.classList.add('active');
        }
    });
});

// Função para formatar datas
function formatDate(dateString) {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('pt-PT', options);
}

// Função para validar ISBN
function validateISBN(isbn) {
    // Remove hífens e espaços
    isbn = isbn.replace(/[-\s]/g, '');
    
    // Verificar se é ISBN-10 ou ISBN-13
    if (isbn.length === 10 || isbn.length === 13) {
        return true;
    }
    return false;
}

// Função para calcular dias restantes
function calculateDaysRemaining(dueDate) {
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Console message
console.log('%c🎓 Biblioteca Online - Agrupamento de Escolas da Batalha', 'color: #0d6efd; font-size: 16px; font-weight: bold;');
console.log('%cDesenvolvido como projeto escolar', 'color: #6c757d; font-size: 12px;');
