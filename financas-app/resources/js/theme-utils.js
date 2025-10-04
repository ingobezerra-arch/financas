// Utilitários para tema escuro dinâmico
window.ThemeUtils = {
    // Aplicar tema a elementos específicos
    applyThemeToElement: function(element, theme) {
        if (theme === 'dark') {
            // Para tabelas que podem ser criadas dinamicamente
            const tables = element.querySelectorAll('.table');
            tables.forEach(table => {
                const thead = table.querySelector('thead');
                if (thead && thead.classList.contains('table-light')) {
                    thead.style.backgroundColor = '#4a5568';
                    thead.style.color = '#ffffff';
                }
            });
            
            // Para cards com bg-light
            const lightCards = element.querySelectorAll('.bg-light');
            lightCards.forEach(card => {
                card.style.backgroundColor = '#4a5568';
                card.style.color = '#ffffff';
            });
            
            // Para badges com bg-light text-dark
            const lightDarkBadges = element.querySelectorAll('.badge.bg-light.text-dark');
            lightDarkBadges.forEach(badge => {
                badge.style.backgroundColor = '#4a5568';
                badge.style.color = '#ffffff';
                badge.style.border = '1px solid #718096';
            });
            
            // Para elementos com text-dark
            const darkTexts = element.querySelectorAll('.text-dark');
            darkTexts.forEach(text => {
                text.style.color = '#ffffff';
            });
            
            // Para table-warning rows
            const warningRows = element.querySelectorAll('.table-warning, tr.table-warning');
            warningRows.forEach(row => {
                row.style.backgroundColor = 'rgba(250, 240, 137, 0.15)';
                row.style.color = '#ffffff';
                
                // Textos dentro da row
                const mutedTexts = row.querySelectorAll('.text-muted, small');
                mutedTexts.forEach(text => {
                    text.style.color = '#cbd5e0';
                });
                
                const strongTexts = row.querySelectorAll('strong');
                strongTexts.forEach(text => {
                    text.style.color = '#ffffff';
                });
            });
        }
    },
    
    // Observer para elementos criados dinamicamente
    observeNewElements: function() {
        const observer = new MutationObserver(function(mutations) {
            const currentTheme = document.body.getAttribute('data-theme') || 'light';
            
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        ThemeUtils.applyThemeToElement(node, currentTheme);
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    },
    
    // Forçar cores de texto em elementos específicos
    enforceTextColors: function() {
        const currentTheme = document.body.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            // Forçar text-muted em tema escuro
            const mutedTexts = document.querySelectorAll('.text-muted');
            mutedTexts.forEach(el => {
                el.style.color = '#a0aec0';
            });
            
            // Forçar text-dark para branco no tema escuro
            const darkTexts = document.querySelectorAll('.text-dark');
            darkTexts.forEach(el => {
                el.style.color = '#ffffff';
            });
            
            // Forçar cores de badge personalizados
            const customBadges = document.querySelectorAll('.badge[style], .badge.bg-light');
            customBadges.forEach(badge => {
                if (badge.classList.contains('bg-light') || badge.style.backgroundColor) {
                    badge.style.color = '#ffffff';
                    badge.style.border = '1px solid rgba(255, 255, 255, 0.2)';
                }
            });
            
            // Corrigir table-warning rows
            const warningRows = document.querySelectorAll('.table-warning, tr.table-warning');
            warningRows.forEach(row => {
                row.style.backgroundColor = 'rgba(250, 240, 137, 0.15)';
                row.style.color = '#ffffff';
                
                const innerTexts = row.querySelectorAll('.text-muted, small');
                innerTexts.forEach(text => {
                    text.style.color = '#cbd5e0';
                });
            });
        }
    }
};

// Inicializar quando DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    ThemeUtils.observeNewElements();
    ThemeUtils.enforceTextColors();
    
    // Re-aplicar quando tema mudar
    document.addEventListener('themeChanged', function(e) {
        setTimeout(() => {
            ThemeUtils.applyThemeToElement(document.body, e.detail.theme);
            ThemeUtils.enforceTextColors();
        }, 100);
    });
});