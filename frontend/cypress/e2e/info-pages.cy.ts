describe('Info Pages', () => {
    it('should load the info page for students', () => {
        cy.visit('/')
        cy.contains("Informácie o odbornej praxi pre študentov").click()
        cy.location('pathname').should('eq', '/info/student')

        cy.contains("Informácie o odbornej praxi pre študentov")
        cy.contains("Podmienky absolvovania predmetu")
    })

    it('should load the info page for companies', () => {
        cy.visit('/')
        cy.contains("Informácie o odbornej praxi pre firmy").click()
        cy.location('pathname').should('eq', '/info/company')

        cy.contains("Detaily a pravidlá odbornej praxe pre firmy")
        cy.contains("Zmluvné podmienky")
        cy.contains("Pravidlá a povinnost počas praxe")
        cy.contains("Hodnotenie a ukončenie praxe")
    })
})