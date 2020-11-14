PROGRESS_FILE=/tmp/dependancy_covidattest_in_progress
if [ ! -z $1 ]; then
	PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "==== Launch install of CovidAttest dependancy ===="
apt-get update
echo 50 > ${PROGRESS_FILE}
echo "Installation ImageMagick"

apt-get install -y imagemagick

echo 90 > ${PROGRESS_FILE}

echo "modify conf file"

path=$(find /etc/ImageMagick* -name policy.xml -execdir pwd  \;)

if [ -d "$path" ]
then
        echo "policy.xml found in $path" > ${PROGRESS_FILE}

        cat "$path/policy.xml" | sed -e 's#<policy domain="coder" rights="none" pattern="PDF" />#<!-- policy domain="coder" rights="none" pattern="PDF" /-->#gi' > "$path/temp-file"

	mv "$path/temp-file" "$path/policy.xml"
	echo "Everything is successfully installed!"
else
        echo "policy.xml not found" > ${PROGRESS_FILE}
fi

echo 100 > ${PROGRESS_FILE}
echo "====== END OF CovidAttest DEPENDENCY INSTALLATION ===="
