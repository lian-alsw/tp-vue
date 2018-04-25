var fs = require('fs');
var path = require('path');
//console.log(path.resolve(__dirname, '../../../../Application/Iview/View/Index/index.html'));
var sourceFile = path.resolve(__dirname, '../../../vant/mobile/dist/index.html');
var destPath = path.resolve(__dirname, '../../../../../Application/Mobile/View/Index/index.html');

// console.log(sourceFile);
// console.log(destPath);
var readStream = fs.createReadStream(sourceFile);
var writeStream = fs.createWriteStream(destPath);
readStream.pipe(writeStream);
console.log("移动完成")
