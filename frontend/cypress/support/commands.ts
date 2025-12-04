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
    cy.contains('th', columnName).parent('tr').then(($headerRow) => {
        const colIndex = $headerRow.find('th').index(cy.$$(`th:contains("${columnName}")`)[0])

        cy.get('tbody tr').each(($row) => {
            cy.wrap($row).find('td').eq(colIndex).invoke('text').then((cellText) => {
                validator(cellText as string)
            })
        })
    })
})