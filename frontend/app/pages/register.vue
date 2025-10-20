<template>

  <v-row class="pc" align="stretch" justify="start">
    <PageCard
      title="Registrácia študenta"
      description="Zaregistruj sa a začni svoju odbornú prax."
      link="/registerStudent"
      icon="mdi mdi-account"
    />
    <PageCard
      title="Registrácia firmy"
      description="Staň sa partnerskou firmou a pomôž študentom získať prax vo svojom odbore."
      link="/registerCompany"
      icon="mdi mdi-office-building"
    />
  </v-row>
</template>

<style scoped>
.pc {
    margin: 0 0 24px 0;
}
</style>
