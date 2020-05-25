User-friendly SVN
================

### Important links
 * Official website: http://www.usvn.info
 * Official instalation instructions: https://github.com/usvn/usvn/wiki/Installation

### Run on Vagrant

**Requirements:**
 * Virtualbox (https://www.virtualbox.org/wiki/Downloads)
 * Vagrant (http://www.vagrantup.com)

**To run:**
```bash
vagrant up
```

**To suspend execution:**
```bash
vagrant suspend
```

**To destroy virtual machine:**
```bash
vagrant destroy
```

### Run USVN installation process again after installed

```bash
vagrant destroy <-- destroy virtual machine
make            <-- remove instalation files
```

### Official maintainers

The project is not maintain. But if you find a security issue or want to contribute we are happy to help.

 * [Julien Duponchelle](https://github.com/noplay)
 * [Stéphane Crivisier](https://github.com/stem)

## Changes

### 1.0.9

* Fix XSS in SVN logs. Credit to [Sysdream](https://www.sysdream.com)
* Solve some problems about binary file, locale and mysql importing.
* Add X-Forwarded-Proto variable check on protocol check
* Added error_log for fail2ban capture by apache-auth filter
* Solve the key length error reported by mysql when import the sql.
* Set the locale by system.locale in the config file.

### 1.0.8
* Fix JVN#73794686 Cross-site scripting vulnerability in

