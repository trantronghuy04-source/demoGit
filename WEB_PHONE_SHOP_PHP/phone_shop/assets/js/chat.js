document.getElementById('chat-form').addEventListener('submit', function(event) {
    event.preventDefault();
    let message = document.getElementById('user-message').value;
    let chatBox = document.getElementById('messages');
    
    if (message) {
        chatBox.innerHTML += `<p><strong>Bạn:</strong> ${message}</p>`;
        document.getElementById('user-message').value = '';
        
        // Giả lập trả lời từ nhân viên tư vấn
        setTimeout(() => {
            chatBox.innerHTML += `<p><strong>Nhân viên:</strong> Xin chào! Chúng tôi có thể giúp gì cho bạn?</p>`;
        }, 1000);
    }
});
