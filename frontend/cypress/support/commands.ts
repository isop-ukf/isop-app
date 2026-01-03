/// <reference types="cypress" />
// ***********************************************
// This example commands.ts shows you how to
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

// Custom command to validate table columns
Cypress.Commands.add('validateColumn', (columnName: string, validator: (value: string) => void) => {
    // Find the column index by matching the header text
    cy.get('thead th').then(($headers) => {
        const headers = $headers.toArray().map((header) => header.innerText.trim());
        const columnIndex = headers.indexOf(columnName);

        if (columnIndex === -1) {
            throw new Error(`Column "${columnName}" not found.  Available columns: ${headers.join(', ')}`);
        }

        // Get all rows in the table body and validate each cell in the target column
        cy.get('tbody tr').each(($row) => {
            cy.wrap($row)
                .find('td')
                .eq(columnIndex)
                .invoke('text')
                .then((cellValue) => {
                    validator(cellValue.trim());
                });
        });
    });
});