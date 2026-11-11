document.addEventListener('DOMContentLoaded', () => {
    
    // Search functionality
    const searchInput = document.getElementById('table-search');
    const searchForm = document.getElementById('search-form');
    const tableRows = document.querySelectorAll('tbody tr');

    if (searchInput && tableRows.length > 0) {
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => e.preventDefault());
        }

        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Live Balance Preview
    const amountInput = document.getElementById('amount');
    const depositRadio = document.getElementById('deposit');
    const withdrawalRadio = document.getElementById('withdrawal');
    const previewAside = document.getElementById('balance-preview');

    if (amountInput && previewAside) {
        const baseBalance = parseFloat(previewAside.getAttribute('data-base-balance'));
        
        const previewLabel = document.getElementById('preview-label');
        const previewAmount = document.getElementById('preview-amount');
        const previewFinal = document.getElementById('preview-final');

        const formatCurrency = (num) => {
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const updatePreview = () => {
            const amount = parseFloat(amountInput.value) || 0;
            const isDeposit = depositRadio.checked;

            const sign = isDeposit ? '+' : '-';
            const newBalance = isDeposit ? baseBalance + amount : baseBalance - amount;
            const textColor = isDeposit ? '#1b4d36' : '#1a1a1a';

            previewLabel.textContent = (isDeposit ? 'Deposit' : 'Withdrawal') + ' amount:';
            
            previewAmount.textContent = sign + '₱' + formatCurrency(amount);
            previewAmount.style.color = textColor;
            
            previewFinal.textContent = '₱' + formatCurrency(newBalance);
        };

        amountInput.addEventListener('input', updatePreview);
        depositRadio.addEventListener('change', updatePreview);
        withdrawalRadio.addEventListener('change', updatePreview);

        updatePreview();
    }

    // Dynamic Navigation Active State
    const navItems = document.querySelectorAll('nav ul li');
    const currentUrl = window.location.href;

    navItems.forEach(item => item.classList.remove('active'));

    navItems.forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            const href = link.getAttribute('href');
            if (href && currentUrl.includes(href)) {
                item.classList.add('active');
            }
        }
    });

    const hasActive = document.querySelector('nav ul li.active');
    if (!hasActive && navItems.length > 0) {
        navItems[0].classList.add('active');
    }

    // Show/Hide Password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('change', function() {
            const type = this.checked ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    }
});