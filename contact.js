// contact.js
document.addEventListener('DOMContentLoaded', () => {
    const form     = document.getElementById('contact-form');
    const feedback = document.getElementById('contact-feedback');
  
    form.addEventListener('submit', e => {
      e.preventDefault();
      feedback.style.display = 'none';
  
      const formData = new FormData(form);
      fetch('contact.php', {
        method: 'POST',
        body:   formData
      })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          feedback.style.color = 'green';
          feedback.textContent = 'Thank you! Your message has been sent.';
          form.reset();
        } else {
          feedback.style.color = 'red';
          feedback.textContent = json.error || 'Failed to send message.';
        }
        feedback.style.display = 'block';
      })
      .catch(err => {
        console.error('Contact send error:', err);
        feedback.style.color = 'red';
        feedback.textContent = 'Network error. Please try again later.';
        feedback.style.display = 'block';
      });
    });
  });