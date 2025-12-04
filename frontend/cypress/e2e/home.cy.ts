describe('Home Page', () => {
    it('should display the home page', () => {
        cy.visit('/')

        cy.wait(1000)

        cy.contains("Domov")
        cy.contains("Register")
        cy.contains("Login")

        cy.contains("Informácie o odbornej praxi pre študentov")
        cy.contains("Informácie o odbornej praxi pre firmy")
        cy.contains("O aplikácii")

        cy.contains("(c) Fakulta prírodných vied a informatiky, UKF v Nitre")
    })
})