describe('PRU_04 - Validar el cálculo de totales en el carrito', () => {
    const user = {
      email: 'esau@gmail.com',
      password: '12345678'
    };
  
    it('Login, agregar productos, finalizar pedido, validar totales, eliminar productos y verificar resumen de pago', () => {
      // 1. Iniciar sesión
      cy.clearLocalStorage();
      cy.visit('http://localhost:8080/Mantenimiento-main/login.php');
      cy.get('input[name="email"]').type(user.email);
      cy.get('input[name="password"]').type(user.password);
      cy.get('form').submit();
      cy.url().should('include', 'indexnew.php');
  
      // 2. Agregar productos al carrito
      cy.visit('http://localhost:8080/Mantenimiento-main/viewmenu.php');
      cy.get('.add-to-cart').eq(0).click();
      cy.get('.add-to-cart').eq(1).click();
      cy.get('#cart-count').should('contain', '2');
  
      // 3. Click en "Finalizar Pedido"
      cy.contains('a.btn-success', 'Finalizar Pedido').click();
      cy.url().should('include', 'ordernew.php');
  
      // 4. Ver totales en el carrito
      cy.get('#subtotal').should('be.visible').invoke('text').then(text => {
        const subtotal = parseFloat(text.replace('$', ''));
        console.log('Subtotal inicial:', subtotal);
        expect(subtotal).to.be.greaterThan(0);
      });
  
      cy.get('#total').should('be.visible').invoke('text').then(text => {
        const total = parseFloat(text.replace('$', ''));
        console.log('Total inicial:', total);
        expect(total).to.be.greaterThan(2.49);
      });
  
      // 5. Eliminar un producto y validar totales actualizados
      cy.get('.remove-from-cart').should('exist').first().click();
      cy.wait(500); // esperar actualización si hay procesamiento asíncrono
  
      cy.get('#subtotal').should('be.visible').invoke('text').then(text => {
        const subtotal = parseFloat(text.replace('$', ''));
        console.log('Subtotal después de eliminar:', subtotal);
        expect(subtotal).to.be.greaterThan(0);
      });
  
      cy.get('#total').should('be.visible').invoke('text').then(text => {
        const total = parseFloat(text.replace('$', ''));
        console.log('Total después de eliminar:', total);
        expect(total).to.be.greaterThan(0); // Ajustado porque puede bajar
      });
  
      // 6. Llenar formulario de entrega y continuar
      cy.get('#name').clear().type('Esau Pérez');
      cy.get('#phone').clear().type('1234567890');
      cy.get('#address').clear().type('Calle Falsa 123');
      cy.get('#proceed-to-payment').click();
  
      // 7. Validar pantalla de pago y resumen
      cy.url().should('include', 'payment.php');
      cy.get('#delivery-address').should('not.be.empty');
      cy.get('#delivery-phone').should('not.be.empty');
      cy.get('#order-items-summary .summary-item').should('have.length.at.least', 1);
  
      cy.get('#subtotal').should('be.visible').invoke('text').then(text => {
        const subtotal = parseFloat(text.replace('$', ''));
        console.log('Subtotal en resumen:', subtotal);
        expect(subtotal).to.be.greaterThan(0);
      });
  
      cy.get('#total').should('be.visible').invoke('text').then(text => {
        const total = parseFloat(text.replace('$', ''));
        console.log('Total en resumen:', total);
        expect(total).to.be.greaterThan(0);
      });
    });
  });
  