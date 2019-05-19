
#!/bin/bash
cd /home/pi/raspimailprint/data

PRINTFILE=$(ls | sort -n | head -1)

if [ ! -z "$PRINTFILE" ]; then

        echo "---------------------"
        echo "Start Printing E-Mail"
        echo "---------------------"
        echo "Print: $PRINTFILE"

        cat $PRINTFILE | sudo python ../mail-printer.py

        echo "Print complete"
        echo "Delete file: $PRINTFILE"

        rm -f $PRINTFILE

        echo "deleted"
fi

echo "Job complete"