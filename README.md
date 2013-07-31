# nDeploy

nDeploy is a phing based deploy scrip for php ecosystem, some idea borrowed from http://capifony.org.
Currently deploy can be started only from a server.

## Basics

### Currently supported frameworks:
- Symfony 2.0.x [vendors based]
- Symfony 2.x [composer based]
- Symfony 1.4
- Yii 1.1.x

### Fetures:
- releases support
- composer support
- shared file handling (symlink based)
- maintenance mode ( http://shiftcommathree.com/articles/make-your-rails-maintenance-page-respond-with-a-503 )
- adding VCS based hash to files
- process locking

## Setup

### 1, Dependencies

- Phing http://www.phing.info/
- VersionControl_Git http://pear.php.net/package/VersionControl_Git

### 2, Install

```
git clone https://github.com/Netpositive/ndeploy.git
```

### 3, Init project

Run command, for config you project:

```
phing -f /path/where/you/installed/ndeploy/build.xml -q
```

Example:

```bash
burci@burci-dev:/srv/example.org$ phing -f /opt/ndeploy/build.xml  -q
     [echo] Wellcome to ndeploy build.properties skeleton generator!
Application name? example
Application basedir [/srv/example.org]?
Application framework (yii,symfony2,symfony,) []?
Releases kept [100]?
SCM type (git,svn) [git]?
SCM repository? ssh://example@git.example.org/example.git
Shared files? vendor,app/config/parameters.yml,app/log.app/data
Vendor type(composer,sf2vendors,custom,none) [composer]?
Vendor command(install,update) [update]?
ndeploy lib [/opt/ndeploy/build.xml]?
     [echo] Edit /srv/example.org/build.properties

BUILD FINISHED

Total time: 9.8417 seconds

## build.properties examples

### Symfony 2, composer based

```
;-- deploy basedir --
basedir=/srv/example.org

;-- application --
application.name=example
application.framework=symfony2
application.repositorydir=/srv/example.org/src/example
application.deploydir=/srv/example.org/current
application.releasesdir=/srv/example.org/releases
application.releaseskept=20

;-- scm proprties --
scm.type=git
scm.repository=ssh://example@git.example.org/example.git
scm.branch=stable

;-- shared files --
shared.files=vendor,app/config/parameters.yml,app/log.app/data

;-- vendor --
vendor=composer
vendor.command=update

;-- maintenance --
maintenance=true
maintenance.source=app/Resources/maintenance.html
maintenance.destination=web/maintenance.html
maintenance.remove=true

;-- hash --
hash=true
hash.file=app/config/parameters_assets.yml,app/config/parameters_assets_2.yml

;-- lock --
lock=true
lock.file=/srv/example.org/releases/ndeploy-example.lock

;-- ndpeloy build target's basedir --
ndeploy.basedir=~/src/ndeploy
```

### Symfony 2.0.x

The difference is the vendor=sf2vendors.

```
;-- deploy basedir --
basedir=/srv/example.org

;-- application --
application.name=example
application.framework=symfony2
application.repositorydir=/srv/example.org/src/example
application.deploydir=/srv/example.org/current
application.releasesdir=/srv/example.org/releases
application.releaseskept=20

;-- scm proprties --
scm.type=git
scm.repository=ssh://example@git.example.org/example.git
scm.branch=stable

;-- shared files --
shared.files=vendor,app/config/parameters.yml,app/log.app/data

;-- vendor --
vendor=sf2vendors
vendor.command=update

;-- maintenance --
maintenance=true
maintenance.source=app/Resources/maintenance.html
maintenance.destination=web/maintenance.html
maintenance.remove=true

;-- hash --
hash=true
hash.file=app/config/parameters_assets.yml,app/config/parameters_assets_2.yml

;-- lock --
lock=true
lock.file=/srv/example.org/releases/ndeploy-example.lock

;-- ndpeloy build target's basedir --
ndeploy.basedir=~/src/ndeploy
```

### Yii 1.x

```
;-- deploy basedir --
basedir=/srv/example.org

;-- application --
application.name=example
application.framework=yii
application.repositorydir=/srv/example.org/src/example
application.deploydir=/srv/example.org/current
application.releasesdir=/srv/example.org/releases
application.releaseskept=20

; -- shared files --
shared.files=project/protected/runtime,project/www/backend/assets,project/www/frontend/assets,project/protected/config/local.php

;-- vendor --
;vendor=composer
;vendor.command=update

;-- maintenance --
maintenance=true
maintenance.source=app/Resources/maintenance.html
maintenance.destination=web/maintenance.html
maintenance.remove=true

;-- hash --
;hash=true
;hash.file=

;-- lock --
lock=true
lock.file=/srv/example.org/releases/ndeploy-example.lock

;-- ndpeloy build target's basedir --
ndeploy.basedir=~/src/ndeploy
```


## Project specific build file

You can include your project specific build file, it will run at the end of the deploy process.
File name mast be ndeploy.xml

### ndeploy.xml hello word example

```xml
<?xml version="1.0" encoding="UTF-8"?>
<?xml-model href="/usr/share/php5/PEAR/data/phing/etc/phing-grammar.rng" type="application/xml" schematypens="http://relaxng.org/ns/structure/1.0" ?>

<project name="cig.sms-container" default="example.project.init">

    <target name="project.example.init">
        <phingcall target="project.example.helloword" />
    </target>

    <target name="project.example.helloword">
        <echo msg="Hello word!" level="warning" />
        <!-- Variables like ${basedir}, ${application.name} can be used -->
    </target>

</project>
```

## TODO

1, Generate change log on deploy, like:
  git log --pretty=format:"%h - %an: %s"
