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
 * [Julien Duponchelle](https://github.com/noplay)
 * [Stéphane Crivisier](https://github.com/stem)

## Changes

1.0.8 Fix JVN#73794686 Cross-site scripting vulnerability in

