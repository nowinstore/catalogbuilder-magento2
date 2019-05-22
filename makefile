
VERSION_NUMBER = 1.0.13

build :
	# cp ./package.xml.tpl ./package.xml
	# sed -i -E "s/VERSION_NUMBER/${VERSION_NUMBER}/g" ./package.xml
	zip -r nowinstore-catalogbuilder-${VERSION_NUMBER}.zip ./ -x '.git/*' -x '.gitignore' -x '.DS_Store'
