# Spectate

## Creating Sites

Creating new Sites in Spectate is a bit of a strange process. If you get the order incorrect, you could end up shutting yourself out of the application by accidentally removing admin privileges, or other strange pitfalls.

The strangeness is to do with their architecture. Instead of users being a single entity, with privileges granted for each Account (aka Site), users are children of Sites. Therefore, you will end up with a separate user in each site that you have access to, and each of those users will have the "Acts as another user" checkbox checked. Strange.

1. In the Spectate menu, go to Administration -> Accounts.
2. Click "Create Account".
3. Enter your names, and for the email and password put anything you like (really, a bogus email is fine). The Company is the name of the site, and the HTTP can also be anything—I haven't been able to see where it is used.
4. Once the site is created, go to it, and go to Admin -> Users. Edit the user you just created, and check "Acts as another user". Select your (admin) user.
5. Create other users as you desire for that site.
6. Create Custom Fields are you desire.
7. And create the form.
8. Remember that in "More Options" you are likely to want to put `@import url(https://www.sarahlawrence.edu/core/v5.0/css/a/spectate.css);` in the Custom CSS field.

