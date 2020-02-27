# Rough-Budget

[Rough-Budget](https://rough-budget.com/) is a simple budgeting tool.

The core functionality is keeping track of your regular expenses, resulting in an estimate of available money per month (or other time unit you like).

The tracking functionality is basic: You can log expenses and incomes, and the tool provides an overview graph and an average of your spending habits of that category.

The budgeting part only allows for the created average value, though.

## Privacy

I created rough-budget as I 

 1. Didn't want a fancy tool that I need to budget for.
 2. Give away my financial information to anyone, just to create a budget.
 
Therefore, privacy is at the very core of Rough-Budget, and it does not require an account to use or track its users in any way, besides normal use of webserver logs.

To make sharing between devices available, I implmented an account feature though.

If you decide to create an account, Rough-Budget will derive 2 keys from your password:

 1. An encryption key, to encrypt all data sent to the server.
 2. An authentication key, to authenticate the read or write of your data in the databse.

A database entry looks like this:

```
ketzu;$2y$10$d/nZqanVqpiPEkCRQNJf6eJTXRl2Y.B6mjwUDqhJSCveJ6E.J0jWW;kw/R1U5FmSTmfqXJ;+9pUIpCRD2e0Rfrt1nA8FybTc0BMgNYoPi+Cb0...
```

It is designed like this, so a database access is not enough to retrieve your data or validate read or write access against the api, i.e., the authentication key is not stored in plaintext.

If the database should leak, an attacker should not be able to use any of the provided information, besides the username.

## Release

Rough-Budget is released as a web project under https://rough-budget.com/ and can be used free of charge.

The beta version, auto compiled and pushed by github actions, is availiable under http://beta.rough-budget.com/

## Project usage

Rough-Budget is a npm based vue project. To test the code locally, use  `npm install` to install all dependencies.

The default hotloading development server is available via:

```npm run serve```

To create a deployable build, use:

`npm run build`

## License

Rough-Budget is available under the MIT license. 

See [the license file](LICENSE) for details.

## Support

Feel free to support me: https://www.paypal.me/roughbudget
