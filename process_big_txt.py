with open("big.txt","r") as fp:
	text = fp.read()
	text = text.strip().split()

f = open("sample.txt","w")

for word in text:
	if(word.isalpha()):
		f.write("%s\n" % word) 
f.close()