//颜色
var li = document.getElementsByTagName('li');
var length = li.length;
var r1 = 250, r = 56 / length;
var g1 = 104, g = 36 / length;
var b1 = 85, b = 13 / length;
var style="<style>";

for (var i = 0; i < length; i++) {
    var rgb = "rgb(" + parseInt(r1) + "," + parseInt(g1) + "," + parseInt(b1) + ")";

    style+= "li:nth-child(" + i + "){background:" + rgb + "}li:nth-child(" + i + ")::after{background:" + rgb + "}";

    r1 -= r;
    g1 -= g;
    b1 -= b;
}
style+="</style>";
console.log(style);
var head=document.getElementsByTagName("head")[0];
console.log(head);
head.innerHTML+=style;


/**
 * @return {string}
 */
function ObjectToHtml1(data) {
    // 若传入数值为json，则转换为字符串
    var txt = typeof data === 'string' ? data : JSON.stringify(data);
    // 转换为Object
    try {
        var obj = eval('(' + txt + ')');
    } catch (e) {
        alert('数据源语法错误,JSON格式化失败! 错误信息: ' + e.description, 'err');
        return
    }
    var line = '</br>', // 换行
        indentChar = '&nbsp;&nbsp;&nbsp;&nbsp;'; // 缩进量
    // 获取缩进字符
    var getTab = function (num) {
        for (var i = 0, tab = ''; i < num; i++) {
            tab += indentChar;
        }
        return tab;
    };
    // 检测递归
    var format = function (value, indent /*缩进*/, isLast /*是否数组或者对象最后*/, inArray /*是否在数组中*/) {
        var str = '';
        // 将处理为`[value,value]`，并将进入检测递归
        if (Object.prototype.toString.call(value) === '[object Array]') {
            str += '[';
            value.forEach(function (item, index) {
                str += format(item, indent, index === (value.length - 1), true);
            });
            str += ']';
            // null，将处理为`<span class="null">null</span>`
        } else if (value === null) {
            str += '<span class="null">null</span>';
            // 对象Object，将处理为`{<span class="key">"key"</span>:value}`，并进入检测递归
        } else if (typeof value === 'object') {
            str += '{' + line + getTab(++indent);
            var keys = Object.keys(value);
            keys.forEach(function (key, index) {
                str += '<span class="key">"' + key + '"</span>: ' + format(value[key], indent, index === (keys.length - 1));
            });
            str += '}';
            indent--;
            // true/false，将处理为`<span class="boolen/null">true/false</span>`
        } else if (typeof value === 'boolean') {
            str += '<span class="boolean">' + value + '</span>';
            // 字符串，将处理为`<span class="string">"string"</span>`
        } else if (typeof value === 'string') {
            str += '<span class="string">"' + value + '"</span>';
            // 数字Number，将处理为`<span class="number">1</span>`
        } else {
            str += '<span class="number">' + value + '</span>';
        }
        str += (isLast ? '' : ',') + (inArray ? '' : (line + getTab(isLast ? --indent : indent)));
        return str;
    };
    return ('<div class="json">' + format(obj, 0, true) + '</div>');
}