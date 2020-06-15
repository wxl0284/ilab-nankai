
function RequestWSString(){
	gameInstance.SendMessage("ParticleLines","SetWSString","http://222.30.40.10");
	gameInstance.SendMessage("ParticleLines","SetPortCalc","http://127.0.0.1:8989");
}

function downloadResult(url)
{
	download(url);
}

function OpenChart(fileID)
{
	window.open("http://222.30.40.10/jsroot/?noselect&layout=grid2x2&file=http://222.30.40.10/"+fileID+"/re.root&items=['posx;1','de;1','ea;1','eb;1']&opts=['e0','hist','hist+logy','']");
}