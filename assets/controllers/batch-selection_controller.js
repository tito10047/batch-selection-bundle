import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

	static targets = ["checkbox", "selectAll"]

	static values = {
		urlToggle: String,
		urlSelectAll: String,
		key: String,
		manager: String
	}

	toggle({event:{params:{id}, target}}){
		let checked = target.value;
		fetch(this._getUrl(this.urlToggleValue, id, checked)).then((response) => {
			if (!response.ok){
				target.value = !checked;
				console.error("can't select item #"+id);
			}
		}).catch((error)=>{
			target.value = !checked;
			console.error(error);
		})
	}

	selectAll({event:{target}}){
		let checked = target.value;
		fetch(this._getUrl(this.urlSelectAllValue, null, checked)).then((response) => {
			if (!response.ok){
				target.value = !checked;
				console.error("can't select all items")
			}else{
				this.checkboxTargets.forEach((checkbox)=>{
					checkbox.value = checked;
				})
			}
		}).catch((error)=>{
			target.value = !checked;
			console.error(error);
		})
	}

	_getUrl(url, id, selected){
		let params = {
			key: this.keyValue,
			manager: this.managerValue,
			selected: selected?"1":"0",
		};
		if (id){
			params["id"]=id;
		}
		return url + '?' + new URLSearchParams(params).toString()
	}
}
