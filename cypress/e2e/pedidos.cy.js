describe('Pedido y verificación en historial simple', () => {
    const user = {
      email: 'esau@gmail.com',
      password: '12345678'
    };
  
    const deliveryInfo = {
      name: 'Esau Pérez',
      phone: '1234567890',
      address: 'Calle Falsa 123'
    };
  
    const paymentData = {
      card: {
        number: '4111111111111111',
        expiry: '12/25',
        cvv: '123',
        name: 'Esau Pérez'
      }
    };
  
    it('Hace login, realiza un pedido y verifica en historial', () => {
      // Login
      cy.visit('http://localhost:8080/Mantenimiento-main/login.php');
      cy.get('input[name="email"]').type(user.email);
      cy.get('input[name="password"]').type(user.password);
      cy.get('form').submit();
      cy.url().should('include', 'indexnew.php');
  
      // Agrega un producto al carrito
      cy.visit('http://localhost:8080/Mantenimiento-main/viewmenu.php');
      cy.get('.add-to-cart').first().click();
  
      // Realiza pedido
      cy.visit('http://localhost:8080/Mantenimiento-main/ordernew.php');
      cy.get('#name').type(deliveryInfo.name);
      cy.get('#phone').type(deliveryInfo.phone);
      cy.get('#address').type(deliveryInfo.address);
      cy.get('#proceed-to-payment').click();
  
      // Completa el pago
      cy.get('#cardNumber').type(paymentData.card.number);
      cy.get('#cardExpiry').type(paymentData.card.expiry);
      cy.get('#cardCvv').type(paymentData.card.cvv);
      cy.get('#cardName').type(paymentData.card.name);
      cy.get('#termsCheck').check();
      cy.get('#confirm-payment').click();
  
      // Verifica redirección
      cy.url({ timeout: 10000 }).should('include', 'indexnew.php');
  
      // Ir al historial
      cy.visit('http://localhost:8080/Mantenimiento-main/historial.php');
  
      // Verificar que hay al menos un pedido
      cy.get('.order-card').should('have.length.at.least', 1);
  
      // Verifica que contenga los elementos clave: pedido, fecha, total
      cy.get('.order-card:first-child').within(() => {
        cy.contains('Pedido #');
    
      });
    });
  });
  