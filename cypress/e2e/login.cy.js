describe('Prueba de Login', () => {
    beforeEach(() => {
      // Visita la página antes de cada test
      cy.visit('http://localhost:8080/Mantenimiento-main/login.php');
    });
  
    it('Login exitoso con credenciales válidas', () => {
      // 1. Llena el formulario
      cy.get('#email').type('esau@gmail.com');
      cy.get('#password').type('12345678');
  
      // 2. Envía el formulario
      cy.get('form').submit();
  
      // 3. Verifica que el login fue exitoso
      cy.url().should('include', '/indexnew.php'); // Redirección
    });
  
    it('Muestra error con credenciales inválidas', () => {
      cy.get('#email').type('usuario@incorrecto.com');
      cy.get('#password').type('contraseñaErronea');
      cy.get('form').submit();
  
      // Verifica el mensaje de error
      cy.get(".alert-danger")
        .should('be.visible')
        .and('contain', 'No existe un usuario con ese email');
    });
  });