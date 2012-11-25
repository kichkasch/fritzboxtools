import MySQLdb
import csv

from config import *

def internationalizeNumber(number, countryCode, localCode):
	if not number or number[0] == "+":
		return number
	if number[0] == "0":
		return countryCode + number[1:]
	return countryCode + localCode + number

# starting connection to database
conn = MySQLdb.connect (host= myHost, user=myUser, passwd=myPw, db=myDB)

# getting the cursor
cursor = conn.cursor()


ifile  = open(fileName, "rb")
reader = csv.reader(ifile, delimiter=';')
rownum = 0

if doLdapLookup:
	import ldap
	l = ldap.initialize("ldap://" + ldapHost)
	l.simple_bind_s('','')

for row in reader:
    # Save header row.
    if rownum > 2:		# data only starts in row 4
	duration = str(int(row[6].split(":")[0])*60 + int(row[6].split(":")[1]))

	callersNumber = row[3]
	callersName = row[2]
	if doLdapLookup and callersNumber and callersNumber[0] != "*":
		callersNumber = internationalizeNumber(callersNumber, countryCode, localCode)
		res = l.search_s(ldapDC, ldap.SCOPE_SUBTREE, 'telephoneNumer=' + callersNumber)
		if len(res):
			callersName = res[0][1]['cn'][0]
		else:
			res = l.search_s(ldapDC, ldap.SCOPE_SUBTREE, 'homePhone=' + callersNumber)
			if len(res):
				callersName = res[0][1]['cn'][0]
			else:
				res = l.search_s(ldapDC, ldap.SCOPE_SUBTREE, 'mobile=' + callersNumber)
				if len(res):
					callersName = res[0][1]['cn'][0]
        cursor.execute ("INSERT IGNORE INTO " + myTable + " VALUES (" + row[0] + ", STR_TO_DATE('" + row[1] + "','%d.%c.%y %H:%i'), '" + callersName + "', '" + row[3] + "', '" + row[5] + "', " + duration + ")")
    rownum += 1

ifile.close()
if doLdapLookup:
	l.unbind()

cursor.close()
conn.close()
