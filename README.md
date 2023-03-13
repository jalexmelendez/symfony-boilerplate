# Shiftmate

Shiftmate is a SaaS that helps companies run smoother day to day operations by allowing internal and external document signing/authorization via biometric signature using the app as well as to monitor the check ins using biometrics and geolocation.

## Table of contents

- Tests
  - PHP Stan
- API Auth

## Tests

### PHP Stan

To Run PHP Stan in the project execute this command:

``` bash
vendor/bin/phpstan analyse src
```

## API Auth

You must include the Authorization Header along with the Bearer JWT token, for example:

``` bash
curl -X 'GET' \
  'http://localhost:8000/api/users/1' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg2NTE5ODQsImV4cCI6MTY3ODY1NTU4NCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoic3RyaW5nQHN0cmluZy5jb20ifQ.VrOZp5cCd6Bfs1BixwFB1fUEA4Dol7ntlPRr6LuvTxSIfh71Q3a7sLhYJSAvax5zWEsqM8ILXvcfD_P2OT682xTLA_ZrdNEccZ1ERJ7sHiSHGGvg7uTmKxP6AFHsRHYhAFd5WWSkREZClGtVkB0Lo1nSKLJlbiN6guXYC7ifSWuQnRRv7ZFp3PWSsgN8K6zS_zHGDSl0q0UHHMUdk8Bun6SFF-lHCTx-iVkHoHLcJlsqnj5DV3BtQGjDwkQYr7_UK69yZHKpnS6PX7ocp__3IkjBejj4wLKtHVCSbe_FhLm0mNq2kW2ia2sr2aCglx7qVi2xcSvfA3tJNgswkiER6A'
 ```
