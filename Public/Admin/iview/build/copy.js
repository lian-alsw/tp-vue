var fs = require('fs');
var path = require('path');
//console.log(path.resolve(__dirname, '../../../../Application/Iview/View/Index/index.html'));
var sourceFile = path.resolve(__dirname, '../../../Admin/iview/dist/index.html');
var destPath = path.resolve(__dirname, '../../../../Application/Iview/View/Index/index.html');

var readStream = fs.createReadStream(sourceFile);
var writeStream = fs.createWriteStream(destPath);
readStream.pipe(writeStream);
console.log("移动完成")
