# TAO Makefile

PHP = /usr/bin/env php --define auto_prepend_file=etc/init.php
PHP_COMPILE = /usr/bin/env php -w
TAO_TEST = bin/tao-run Dev.Unit.Run.Text

export TAO_CORE=lib/Core.php
export TAO_PATH=-Test:test/lib;*:lib

# Modules install
.PHONY : install
install:
ifdef PREFIX
	@ echo -n "Generating bin lib etc..."
	@ mkdir -p $(PREFIX)/bin $(PREFIX)/lib $(PREFIX)/etc $(PREFIX)/etc/eclipse
	@ echo "ok"

	@ echo -n "Copying etc..."
	@ cp -f etc/init.php $(PREFIX)/etc/init.php
	@ cp -f etc/eclipse/templates.xml $(PREFIX)/etc/eclipse/templates.xml
	@echo "ok"

	@ echo -n "Generating php-tao..."
	@ printf "#!/bin/sh \n\
	export TAO_HOME=$(PREFIX) \n\
	TAO_PATH=\"\$$TAO_PATH\$${TAO_PATH:+;}-Test:test;*:$(PREFIX)/lib\" exec /usr/bin/env php --define auto_prepend_file=\$$TAO_HOME/etc/init.php \$$@" > $(PREFIX)/bin/php-tao
	@ echo "ok"
	@ echo -n "Configuring tao-run..."
	@ sed -e 's@#!.*@#!/usr/bin/env $(PREFIX)/bin/php-tao@' bin/tao-run > $(PREFIX)/bin/tao-run 
	@ echo "ok"

	@ echo -n "Generating service scripts..."
	@ printf "#!/bin/sh\n$(PREFIX)/bin/tao-run Dev.Source.Diagram \$$@" > $(PREFIX)/bin/tao-source-diagram
	@ printf "#!/bin/sh\n$(PREFIX)/bin/tao-run Dev.Unit.Text \$$@" > $(PREFIX)/bin/tao-test
	@ printf "#!/bin/sh\n$(PREFIX)/bin/tao-run Dev.DB.Diagram \$$@" > $(PREFIX)/bin/tao-db-diagram
	@ cp -f bin/tao-stages $(PREFIX)/bin/tao-stages
	@ find  $(PREFIX)/bin -type f | xargs chmod +x
	@ echo "ok"

	@ echo -n "Creating lib tree..."
	@ find lib -type d | sed -e 's|^|$(PREFIX)/|' | xargs mkdir -p 
	@ echo "ok"
	@ echo -n "Installing modules..."
	@ find lib -type f -name '*.php' | sed -e 's|\(.*\)|$(PHP_COMPILE) \1 > $(PREFIX)/\1|' | sh
	@ echo "ok"
else
	@ echo 'Error: PREFIX is not set, installation cancelled'
endif

# Unit testing
.PHONY : test
test:
	@ find test/lib  -name '*.php' | sed -e  's|^test/lib/||;s|\.php$$||;s|/|.|g' | xargs $(TAO_TEST)
