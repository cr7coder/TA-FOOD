document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            fetch('/api/v1/admin/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Tạo người dùng thành công!');
                    window.location.href = '/admin/users';
                } else {
                    alert('Lỗi: ' + (data.message || 'Đã xảy ra lỗi.'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    
                    // Handle validation errors if any
                    if (data.errors) {
                        let errorMsg = '';
                        for (const key in data.errors) {
                            errorMsg += `\n- ${data.errors[key][0]}`;
                        }
                        alert('Chi tiết lỗi:' + errorMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi kết nối.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});
