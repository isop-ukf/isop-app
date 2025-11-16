describe('Admin Student Document Downloads', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(1000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Praxe").click()
        cy.url().should('include', '/dashboard/admin/internships')
    })

    it('should be able to generate and download the default agreement', () => {
        cy.get('table').within(() => {
            cy.get('tbody tr')
                .then(rows => {
                    const count = rows.length
                    const randomIndex = Math.floor(Math.random() * count)
                    cy.wrap(rows[randomIndex]).as('randomRow')
                })

            cy.get('@randomRow').within(() => {
                cy.get('td').contains('Editovať').click()
            })
        })

        cy.url().should('include', '/dashboard/admin/internships/edit/')

        const downloadsFolder = Cypress.config("downloadsFolder");
        cy.contains('Stiahnuť originálnu zmluvu').click()
    })
})