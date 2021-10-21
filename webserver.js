const express = require('express')
const app = express()
// const port = 3000
// const path = require('path')

var execPHP = require('./execphp.js')();

execPHP.phpFolder = 'C:\\Users\\jaysondurante\\coding-projects\\ovr\\ovr-github\\OVRRIDE\\wp-content';

app.use('*.php',function(request,response,next) {
	execPHP.parseFile(request.originalUrl,function(phpResult) {
		response.write(phpResult);
		response.end();
	});
});


app.listen(3000, function () {
	console.log('Node server listening on port 3000!');
});


// app.engine( 'ejs', engine );
// app.set( 'view engine', 'ejs' );
// app.engine('php', phpnode);
// app.set('view engine', 'php');

// app.use(express.static('./ovrride'))
// app.use(express.static('public'))

// app.get('/', (req, res) => {
//     res.send(path.resolve('./wp-content/themes/index.php'))
// })

// app.get('/', (req, res) => {
//   res.send('Hello World!')
// })

// app.listen(port, () => {
//   console.log(`Example app listening at http://localhost:${port}`)
// })