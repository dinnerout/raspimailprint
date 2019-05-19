cd /home/pi/raspimailprint/

# sudo is not nice but needs to be set to dialout via ttyACM0
# sudo usermod -a -G dialout pi - is not working? (why? fix!)
sudo php mailImporter.php