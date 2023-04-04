# Pressbooks HfH Plugin

This WordPress plugin extends the Pressbooks plugin with additional features used by the HfH Zuugs platform.

## Features

### Subscriber Management

Using the [Shibboleth plugin](https://wordpress.org/plugins/shibboleth/), users belonging to a certain organization can be granted subscriber permissions automatically. The groups being able to access a certain book can be selected at `Settings > Privacy`.

For this feature to work properly, a small modification needs to be made to the source code of the Shibboleth plugin.

Implemented in `components/user.php`.

