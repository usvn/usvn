clean: .clear
	@rm -f src/config/config.ini
	@rm -f src/public/.htaccess

rw:
	@chmod 777 src/config/config.ini

.clear:
	@clear
