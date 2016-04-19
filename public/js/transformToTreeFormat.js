/*settings,指定当前节点名称,父节点名称,孩子节点名称
nodes,json对象
返回树结构的json串
*/
function transformTozTreeFormat(settings,nodes) {
		var i,l,
		key = settings.keyName,
		parentKey = settings.parentKeyName,
		childKey = settings.childKeyName;
		if (!key || key=="" || !nodes) return [];
		var r = [];
		var tmpMap = [];
		for (i=0, l=nodes.length; i<l; i++) {
			tmpMap[nodes[i][key]] = nodes[i];
		}
		for (i=0, l=nodes.length; i<l; i++) {
			if (tmpMap[nodes[i][parentKey]] && nodes[i][key] != nodes[i][parentKey]) {
				if (!tmpMap[nodes[i][parentKey]][childKey])
					tmpMap[nodes[i][parentKey]][childKey] = [];
				tmpMap[nodes[i][parentKey]][childKey].push(nodes[i]);
			} else {
				r.push(nodes[i]);
			}
		}
		return JSON.stringify(r);
}