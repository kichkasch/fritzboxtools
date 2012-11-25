import MySQLdb
import csv

myHost = "192.168.200.20"
myUser = "fb_user"
myPw = "****"
myDB = "fb_db"
myTable = "calllog"

fileName = "/tmp/fb_anrufliste.csv"

# starting connection to database
conn = MySQLdb.connect (host= myHost, user=myUser, passwd=myPw, db=myDB)

# getting the cursor
cursor = conn.cursor()


ifile  = open(fileName, "rb")
reader = csv.reader(ifile, delimiter=';')
rownum = 0
for row in reader:
    # Save header row.
    if rownum > 2:		# data only starts in row 4
	duration = str(int(row[6].split(":")[0])*60 + int(row[6].split(":")[1]))
        cursor.execute ("INSERT IGNORE INTO " + myTable + " VALUES (" + row[0] + ", STR_TO_DATE('" + row[1] + "','%d.%c.%y %H:%i'), '" + row[2] + "', '" + row[3] + "', '" + row[5] + "', " + duration + ")")
    rownum += 1

ifile.close()

cursor.close()
conn.close()
