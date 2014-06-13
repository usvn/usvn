User-frindly SVN
================

### Important links
 * Official website: http://www.usvn.info
 * Official instalation instructions: https://github.com/usvn/usvn/wiki/Installation

### Run on Vagrant

**Requirements:**
 * Virtualbox (https://www.virtualbox.org/wiki/Downloads)
 * Vagrant (http://www.vagrantup.com)

**To run:**
```shell
vagrant up
```

**To suspend execution:**
```shell
vagrant suspend
```

**To destroy virtual machine:**
```shell
vagrant destroy
```

### Run USVN installation process again after installed

```shell
vagrant destroy <-- destroy virtual machine
make            <-- remove instalation files
```
