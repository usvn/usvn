rw: clean
	@echo '[general]' > src/config/config.ini
	@chmod 777 src/config/config.ini
	@chmod 777 src/config
	@chmod 777 src/public

clean: .clear
	@rm -f src/public/.htaccess
	@rm -f src/config/config.ini

.clear:
	@clear
