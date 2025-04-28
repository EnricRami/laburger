// script.js
document.addEventListener('DOMContentLoaded', () => {
  // --- POPUP LOGIC ---
  const wrapper      = document.querySelector('.wrapper');
  const loginLink    = document.querySelector('.login-link');
  const registerLink = document.querySelector('.register-link');
  const btnPop       = document.querySelector('.btnLogin-popup');
  const iconClose    = document.querySelector('.icon-close');
  const logoutBtn    = document.getElementById('logout-btn');

  registerLink.addEventListener('click', () => wrapper.classList.add('active'));
  loginLink   .addEventListener('click', () => wrapper.classList.remove('active'));
  btnPop      .addEventListener('click', () => wrapper.classList.add('active-popup'));
  iconClose   .addEventListener('click', () => wrapper.classList.remove('active-popup'));

  // --- AUTH UI UPDATE ---
  function updateAuthUI() {
    const isLoggedIn = !!localStorage.getItem('laburgerUserId');
    const loginBtn   = document.querySelector('.btnLogin-popup');

    if (isLoggedIn) {
      loginBtn.style.display  = 'none';
      logoutBtn.style.display = 'inline-block';
    } else {
      loginBtn.style.display  = 'inline-block';
      logoutBtn.style.display = 'none';
    }
  }
  updateAuthUI();

  // --- LOGIN LOGIC ---
  document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('login.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Save user ID (user or admin) & email
          localStorage.setItem('laburgerUserId', data.user_id || data.admin_id);
          localStorage.setItem('laburgerUserEmail', formData.get('email'));
          alert('Login successful!');
          wrapper.classList.remove('active-popup');
          updateAuthUI();
          // Redirect if admin
          if (data.role === "admin") {
              window.location.href = "admin_dashboard.html"; // or admin_dashboard.php
          } else {
              // Optionally, do something for regular users
              // For example, reload page or close modal
          }
        } else {
          alert(data.message || 'Login failed!');
        }
      })
      .catch(err => {
        console.error('Login error:', err);
        alert('Login error!');
      });
  });

  // --- REGISTER LOGIC ---
  document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('register.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Registration successful! Please log in.');
          wrapper.classList.remove('active');
        } else {
          alert(data.message || data.error || 'Registration failed!');
        }
      })
      .catch(err => {
        console.error('Registration error:', err);
        alert('Registration error!');
      });
  });

  // --- LOGOUT LOGIC ---
  function logout() {
    localStorage.removeItem('laburgerUserId');
    localStorage.removeItem('laburgerUserEmail');
    updateAuthUI();
    alert('Logged out!');
    // Optionally, redirect to home
    // window.location.href = 'index.html';
  }
  logoutBtn.addEventListener('click', logout);

  // --- CART LOGIC ---
  let cart = JSON.parse(localStorage.getItem('laburgerCart')) || [];

  const cartIcon     = document.getElementById('cart-icon');
  const cartCount    = document.getElementById('cart-count');
  const cartModal    = document.getElementById('cart-modal');
  const closeCart    = document.getElementById('close-cart');
  const cartItemsEl  = document.getElementById('cart-items');
  const checkoutBtn  = document.querySelector('.checkout-btn');

  // Open/close cart
  cartIcon.addEventListener('click', () => cartModal.style.display = 'block');
  closeCart.addEventListener('click', () => cartModal.style.display = 'none');

  // Add to cart
  function attachAddToCart() {
    document.querySelectorAll('.add-to-cart').forEach((button, idx) => {
      button.addEventListener('click', () => {
        const itemEl   = button.closest('.menu-item');
        const name     = itemEl.querySelector('h3').textContent;
        const price    = parseFloat(itemEl.querySelector('span').textContent.replace('$',''));
        const image    = itemEl.querySelector('img').src;
        const existing = cart.find(i => i.name === name);
        if (existing) {
          existing.quantity++;
        } else {
          cart.push({ id: idx+1, name, price, image, quantity: 1 });
        }
        updateCart();
      });
    });
  }
  attachAddToCart();

  function updateCart() {
    cartItemsEl.innerHTML = '';
    let total = 0;
    if (cart.length === 0) {
      cartItemsEl.innerHTML = '<p>Your cart is empty!</p>';
      cartCount.textContent = 0;
      localStorage.setItem('laburgerCart', JSON.stringify(cart));
      return;
    }
    cart.forEach(item => {
      total += item.price * item.quantity;
      const div = document.createElement('div');
      div.className = 'cart-item';
      div.innerHTML = `
        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
        <div class="cart-item-info">
          <h4>${item.name}</h4>
          <p class="cart-item-price">$${(item.price * item.quantity).toFixed(2)}</p>
        </div>
        <div class="cart-controls">
          <button class="decrease-btn">-</button>
          <span class="quantity">${item.quantity}</span>
          <button class="increase-btn">+</button>
        </div>
      `;
      // quantity controls
      div.querySelector('.increase-btn').addEventListener('click', () => {
        item.quantity++;
        updateCart();
      });
      div.querySelector('.decrease-btn').addEventListener('click', () => {
        if (item.quantity > 1) item.quantity--;
        else cart = cart.filter(i => i !== item);
        updateCart();
      });
      cartItemsEl.appendChild(div);
    });
    // total
    const totEl = document.createElement('div');
    totEl.className = 'cart-total';
    totEl.innerHTML = `<strong>Total: $${total.toFixed(2)}</strong>`;
    cartItemsEl.appendChild(totEl);

    cartCount.textContent = cart.reduce((sum, i) => sum + i.quantity, 0);
    localStorage.setItem('laburgerCart', JSON.stringify(cart));
  }
  updateCart();

  // --- CHECKOUT (multi-step handled in checkout.js) ---
  checkoutBtn.addEventListener('click', () => {
    if (!localStorage.getItem('laburgerUserId')) {
      alert('You must be logged in to place an order!');
      btnPop.click();
      return;
    }
    if (cart.length === 0) {
      alert('Your cart is empty!');
      return;
    }
    localStorage.setItem('laburgerCart', JSON.stringify(cart));
    window.location.href = 'checkout.html';
  });
});