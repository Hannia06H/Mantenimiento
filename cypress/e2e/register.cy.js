describe('Registro en Pizzería Deliciosa', () => {
  beforeEach(() => {
    cy.visit('http://localhost:8080/Mantenimiento-main/register.php'); // Ajusta la URL según tu entorno
  });

  // Helper para llenar datos básicos
  const fillBasicInfo = () => {
    cy.get('#firstName').type('Hann');
    cy.get('#lastName').type('Hernandez');
    cy.get('#email').type(`test${Math.floor(Math.random() * 10000)}@gmail.com`);
    cy.get('#phone').type('7714097913');
  };

  // Caso 1: Registro exitoso
  it('Registro completo con datos válidos', () => {
    fillBasicInfo();
    cy.get('#password').type('Pizza1234');
    cy.get('#confirmPassword').type('Pizza1234');
    cy.get('#termsCheck').check();
    cy.contains('Registrarse').click();

    // Verificaciones
    cy.get('.alert-success').should('contain', '¡Registro exitoso!');
    cy.url().should('include', '/login.php');
  });

  

  // Caso 3: Validación de contraseña
  it('Rechaza contraseñas cortas', () => {
    fillBasicInfo();
    cy.get('#password').type('123');
    cy.get('#confirmPassword').type('123');
    cy.get('#termsCheck').check();
    cy.contains('Registrarse').click();

    cy.get('.alert-danger').should('contain', 'al menos 8 caracteres');
  });

  // Caso 4: Coincidencia de contraseñas
  it('Detecta contraseñas no coincidentes', () => {
    fillBasicInfo();
    cy.get('#password').type('Pizza1234');
    cy.get('#confirmPassword').type('Pizza0000');
    cy.get('#termsCheck').check();
    cy.contains('Registrarse').click();

    cy.get('.alert-danger').should('contain', 'no coinciden');
  });

  // Caso 5: Términos y condiciones - Versión corregida
it('Bloquea registro sin aceptar términos', () => {
    fillBasicInfo();
    cy.get('#password').type('Pizza1234');
    cy.get('#confirmPassword').type('Pizza1234');
    
    cy.contains('Registrarse').click();
  
    
    // Verificación 3: Que no haya redirección
    cy.url().should('include', 'register.php');
  });

  // Caso 6: Email duplicado
  it('Detecta correos electrónicos ya registrados', () => {
    const existingEmail = 'hannia@gmail.com'; // Usa un email que ya exista
    
    fillBasicInfo();
    cy.get('#email').clear().type(existingEmail);
    cy.get('#password').type('Pizza1234');
    cy.get('#confirmPassword').type('Pizza1234');
    cy.get('#termsCheck').check();
    cy.contains('Registrarse').click();

    cy.get('.alert-danger').should('contain', 'ya está registrado');
  });
});