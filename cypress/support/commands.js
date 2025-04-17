// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
Cypress.Commands.add('registroCompleto', (userData) => {
    cy.visit('/register.php');
    cy.get('#firstName').type(userData.nombre);
    cy.get('#lastName').type(userData.apellido);
    cy.get('#email').type(userData.email);
    cy.get('#phone').type(userData.telefono || '');
    cy.get('#password').type(userData.password);
    cy.get('#confirmPassword').type(userData.password);
    if (userData.aceptarTerminos !== false) {
      cy.get('#termsCheck').check();
    }
    cy.contains('Registrarse').click();
  });

  Cypress.Commands.add('login', (email, password) => {
    cy.visit('/login.php');
    cy.get('input[name="email"]').type(email);
    cy.get('input[name="password"]').type(password);
    cy.get('form').submit();
    cy.url().should('include', 'indexnew.php');
  });
  
  Cypress.Commands.add('setupOrder', (deliveryInfo) => {
    cy.visit('/viewmenu.php');
    cy.get('.add-to-cart').first().click();
    cy.get('.add-to-cart').eq(1).click();
    cy.visit('/ordernew.php');
    cy.get('#name').type(deliveryInfo.name);
    cy.get('#phone').type(deliveryInfo.phone);
    cy.get('#address').type(deliveryInfo.address);
    cy.get('#proceed-to-payment').click();
  });

  