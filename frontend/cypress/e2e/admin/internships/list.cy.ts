describe('Admin Internship CRUD', () => {
    beforeEach(() => {
        cy.visit('/login')

        cy.wait(3000)

        cy.get('input[type="email"]').type('test@example.com')
        cy.get('input[type="password"]').type('password')
        cy.get('button[type="submit"]').click()

        cy.wait(1000)

        cy.contains("Praxe").click()
        cy.url().should('include', '/dashboard/admin/internships')

        cy.wait(2000)
    })

    it('should load the list of internships in a proper format', () => {
        cy.get('table').within(() => {
            cy.validateColumn('Firma', (company) => {
                expect(company).to.not.be.empty
            })

            cy.validateColumn('Študent', (student) => {
                const nameParts = student.trim().split(' ')
                expect(nameParts).to.have.length.at.least(2)
                expect(nameParts[0]).to.not.be.empty
                expect(nameParts[1]).to.not.be.empty
            })

            cy.validateColumn('Od', (from) => {
                const datePattern = /^\d{2}\.\d{2}\.\d{4}$/
                expect(from.trim()).to.match(datePattern)
                const [day, month, year] = from.trim().split('.').map(Number)
                const date = new Date(year, month - 1, day)
                expect(date.getDate()).to.equal(day)
                expect(date.getMonth()).to.equal(month - 1)
                expect(date.getFullYear()).to.equal(year)
            })

            cy.validateColumn('Do', (to) => {
                const datePattern = /^\d{2}\.\d{2}\.\d{4}$/
                expect(to.trim()).to.match(datePattern)
                const [day, month, year] = to.trim().split('.').map(Number)
                const date = new Date(year, month - 1, day)
                expect(date.getDate()).to.equal(day)
                expect(date.getMonth()).to.equal(month - 1)
                expect(date.getFullYear()).to.equal(year)
            })

            cy.validateColumn('Ročník', (grade) => {
                const gradeNum = Number(grade.trim())
                expect(gradeNum).to.be.a('number')
                expect(gradeNum).to.be.at.least(1)
                expect(gradeNum).to.be.at.most(5)
            })

            cy.validateColumn('Semester', (semester) => {
                expect(semester.trim()).to.be.oneOf(['Zimný', 'Letný'])
            })

            // stav netestujeme

            cy.contains('th', 'Operácie').parent('tr')
        })
    })

    it('should be able to delete an internship', () => {
        cy.get('table tbody tr').first().within(() => {
            cy.get('.internship-delete-btn').click()
        })

        cy.contains("Potvrdiť vymazanie").parent().should('be.visible')
        cy.contains("Potvrdiť vymazanie").parent().contains("Áno").click()
        cy.contains("Potvrdiť vymazanie").should('not.exist')

        cy.wait(2000)
    })

    // TODO: Edit praxe
})