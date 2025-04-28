// checkout.js
document.addEventListener('DOMContentLoaded', () => {
    // 1) Grab DOM elements
    const step1        = document.getElementById('step-1');
    const step2        = document.getElementById('step-2');
    const step3        = document.getElementById('step-3');
    const stepper1     = document.getElementById('stepper-1');
    const stepper2     = document.getElementById('stepper-2');
    const stepper3     = document.getElementById('stepper-3');
    const nextBtn      = document.getElementById('next-step');
    const placeBtn     = document.getElementById('place-order');
    const delForm      = document.getElementById('delivery-form');
    const payForm      = document.getElementById('payment-form');
    const summaryItems = document.getElementById('summary-items');
    const summaryTotal = document.querySelector('#summary-total span:last-child');
  
    // 2) Load cart from localStorage (or empty array)
    const rawCart = localStorage.getItem('laburgerCart');
    console.log('🔍 raw localStorage.laburgerCart:', rawCart);
    let cart = rawCart ? JSON.parse(rawCart) : [];
    console.log('🔍 parsed cart array:', cart);
  
    // 3) Function to render the sidebar summary and compute total
    function renderSummary() {
      console.log('🔍 renderSummary() cart:', cart);
      summaryItems.innerHTML = '';
      let total = 0;
  
      if (!cart.length) {
        summaryItems.innerHTML = '<p>Your cart is empty.</p>';
        summaryTotal.textContent = '$0.00';
        console.log('📭 cart is empty, total = $0.00');
        return;
      }
  
      cart.forEach(item => {
        // Ensure price & quantity are numbers
        const price = parseFloat(item.price) || 0;
        const qty   = parseInt(item.quantity, 10) || 0;
        const line  = price * qty;
        console.log(`➕ ${item.name}: ${price} × ${qty} = ${line.toFixed(2)}`);
        total += line;
  
        // Create the DOM row
        const row = document.createElement('div');
        row.className = 'summary-item';
        row.innerHTML = `
          <span>${item.name} × ${qty}</span>
          <span>$${line.toFixed(2)}</span>
        `;
        summaryItems.appendChild(row);
      });
  
      console.log('✅ computed total:', total.toFixed(2));
      summaryTotal.textContent = `$${total.toFixed(2)}`;
    }
  
    // 4) Step‐showing utility
    function showStep(n) {
      step1.style.display    = n === 1 ? '' : 'none';
      step2.style.display    = n === 2 ? '' : 'none';
      step3.style.display    = n === 3 ? '' : 'none';
      stepper1.classList.toggle('active', n === 1);
      stepper2.classList.toggle('active', n === 2);
      stepper3.classList.toggle('active', n === 3);
      window.scrollTo(0, 0);
    }
  
    // 5) Initialize to Step 1 and render summary
    showStep(1);
    renderSummary();
  
    // 6) Step 1 → Step 2
    nextBtn.addEventListener('click', () => {
      if (!delForm.checkValidity()) {
        delForm.reportValidity();
        return;
      }
      showStep(2);
    });
  
    // 7) Step 2 → Place Order → Step 3
    placeBtn.addEventListener('click', () => {
      // Validate payment fields
      if (!payForm.checkValidity()) {
        payForm.reportValidity();
        return;
      }
  
      // Optionally check login:
      const userId = localStorage.getItem('laburgerUserId');
      if (!userId) {
        alert('Please log in before placing an order.');
        window.location.href = 'index.html';
        return;
      }
  
      // Disable the button to avoid double‐clicks
      placeBtn.disabled    = true;
      placeBtn.textContent = 'Placing Order…';
  
      // Gather all data
      const delivery = {
        name:    delForm['full-name'].value,
        email:   delForm['email'].value,
        phone:   delForm['phone'].value,
        address: delForm['address'].value,
        notes:   delForm['notes'].value
      };
      const payment = {
        card_name:   payForm['card-name'].value,
        card_number: payForm['card-number'].value,
        expiry:      payForm['card-expiry'].value,
        cvv:         payForm['card-cvv'].value
      };
      const payload = { user_id: userId, delivery, payment, cart };
  
      console.log('🚀 sending order payload:', payload);
  
      // Send to server
      fetch('order.php', {
        method:  'POST',
        headers: { 'Content-Type':'application/json' },
        body:    JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(json => {
        console.log('📨 server response:', json);
        if (json.success) {
          // Clear cart in storage and memory
          localStorage.removeItem('laburgerCart');
          cart = [];
          renderSummary();
          // Show confirmation
          showStep(3);
        } else {
          alert('Order failed: ' + (json.message || 'Unknown error'));
          placeBtn.disabled    = false;
          placeBtn.textContent = 'Place Order';
        }
      })
      .catch(err => {
        console.error('❌ network/order error:', err);
        alert('Network error placing order.');
        placeBtn.disabled    = false;
        placeBtn.textContent = 'Place Order';
      });
    });
  });