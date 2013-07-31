# nDeploy

Example: build.properties.dist

## ndeploy.xml example

Migrating two yii embedded application

```xml
<?xml version="1.0" encoding="UTF-8"?>
<?xml-model href="/usr/share/php5/PEAR/data/phing/etc/phing-grammar.rng" type="application/xml" schematypens="http://relaxng.org/ns/structure/1.0" ?>

<project name="cig.sms-container" default="cig.sms-container.init">

    <import file="${ndeploy.basedir}/target/ndeploy.framework.yii.xml"/>

    <target name="cig.sms-container.init">
        <phingcall target="cig.sms-container.migrate" />
    </target>

    <target name="cig.sms-container.migrate">
        <property name="application.framework.extra.migrate" value="true" override="true" />

        <property name="application.framework.extra.migrate.command" value="./app1/protected/yiic" override="true" />
        <phingcall target="ndeploy.framework.yii.migrate" />

        <property name="application.framework.extra.migrate.command" value="./app2/protected/yiic" override="true" />
        <phingcall target="ndeploy.framework.yii.migrate" />
    </target>

</project>
```

## Nice to do

1, Generate change log, like:

  git log --pretty=format:"%h - %an: %s"
