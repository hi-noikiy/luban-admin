@extends('admin::layout')

@section('title', $finder->title() )

@section('action-bar')
<div style="display: flex">
	<div style="flex:0 0 auto">
		<div class="btn-group" role="group">
			<a v-for="(action, idx) in finder.actions" class="btn btn-default" v-bind:href="action.url">
				@{{action.label}}
			</a>
		</div>
	</div>
	<div class="finder-tabber">
		  <ul class="nav nav-tabs" role="tablist">
		    <li v-for="(panel, tab_id) in finder.tabs" v-bind:class="{'active': tab_id==current_tab}">
		    	<a v-on:click="select_tab(tab_id)">@{{panel.label}}</a>
		    </li>
		    @if (array_key_exists('search', View::getSections()))
		    <li v-bind:class="{'active': 'searchbar'==current_tab}">
		    	<a v-on:click="select_tab('searchbar')">
			    	<i class="glyphicon glyphicon-search"></i>
			    	搜索
		    	</a>
		    </li>
		    @endif
			<li v-bind:class="{'active': 'workdesk'==current_tab}">
		    	<a v-on:click="select_tab('workdesk')">
					<i class="glyphicon glyphicon-duplicate"></i>		    	
		    		操作台
		    	</a>
		    </li>
		  </ul>
	</div>
</div>
@endsection

@section('header')
<div class="finder-header">
	<div class="finder-search-bar" v-if="'searchbar'==current_tab">
		@yield('search')
	</div>
	<div class="finder-workdesk-bar" v-if="'workdesk'==current_tab">
		操作台: 一个临时收纳台.
	</div>
	<div class="finder-row">
		<label class="finder-col-sel" v-if="finder.batchActions.length>0">
			<input type="checkbox" v-on:click="select_all" v-model="v_select_all" />
		</label>
		<div class="row api-top-title">
			<div v-for="(col, col_id) in finder.cols" v-bind:class="col_class[col_id]">
				@{{col.label}}
			</div>
		</div>
	</div>
</div>
@endsection

@section('footer')
<div style="height:3rem; position: relative; line-height: 3rem; padding:0 0.5rem">
	<div>
	页码
	</div>
</div>
@endsection

@section('content')
<div class="finder-body">

	<div class="finder-item" v-for="(item,idx) in finder.items" v-bind:class="{'selected':selected[idx], 'detail':current_detail==idx}">
		<div class="finder-row">
			<label class="finder-col-sel" v-if="finder.batchActions.length>0">
				<input type="checkbox" v-model="selected[idx]" />
			</label>
			<div class="row api-top-title" v-on:click="toggle_detail(idx)">
				<div v-for="(col, col_id) in finder.cols" v-bind:class="col_class[col_id]">
					@{{item[col_id]}}
				</div>
			</div>
		</div>
		<div class="finder-detail" v-if="current_detail==idx">
			  <ul class="nav nav-tabs" role="tablist">
			    <li v-for="(panel, panel_id) in finder.infoPanels" v-bind:class="{'active': panel_id==current_panel}">
			    	<a v-on:click="show_panel(idx, panel_id)">@{{panel.label}}</a>
			    </li>
			  </ul>
			  <div class="tab-content">
			    <div role="tabpanel" class="tab-pane active" v-for="(panel, panel_id) in finder.infoPanels" v-if="panel_id==current_panel">
			    	<div class="finder-detail-content" v-html="item.panels[panel_id]"></div>
			    </div>
			  </div>
		</div>
	</div>

</div>

<div class="finder-batch-action-bar" v-if="selected_count>0">
	<div>
		<span>
		已选择: @{{selected_count}}项
		</span>

		<div class="btn-group" role="group">
			<button v-for="(action, idx) in finder.batchActions" class="btn btn-default btn-sm">@{{action.label}}</button>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
$(function(){
	var app = new Vue({
	  el: '.main-content',
	  mounted: function(){
	  },
	  computed: {
	  	col_class: function(){
	  		ret = [];
	  		for(var i=0;i<this.finder.cols.length;i++){
	  			var obj = {};
	  			if(this.finder.cols[i].className){
	  				obj[this.finder.cols[i].className] = true;
	  			}
	  			obj['col-md-'+this.finder.cols[i].size] = true;
	  			ret[i] = obj;
	  		}
	  		return ret;
	  	},
	  	selected_count: function(){
	  		var ret = 0;
	  		for(var i=0; i<this.selected.length;i++){
	  			if(this.selected[i]==true){
	  				ret++;
	  			}
	  		}
	  		return ret;
	  	}
	  },
	  methods:{
		select_all: function(e){
			if(this.v_select_all){
				for(var i=0;i<this.finder.items.length;i++){
					this.$set(this.selected, i, true);
				}
			}else{
				this.selected = [];
			}
		},
		toggle_detail: function(id){
			if(this.current_detail==id){
				this.current_detail = undefined;
			}else{
				this.current_detail = id;
				this.show_panel(id, 0);
			}
		},
		select_tab: function(tab_id){
			this.current_tab = tab_id;
			this.selected = [];
			this.v_select_all = false;
		},
		show_panel: function(item_idx, panel_id){		
			if(!this.finder.items[item_idx]){
				return;
			}
			if(!this.finder.items[item_idx].panels){
				this.$set(this.finder.items[item_idx], 'panels', {});
			}
			if(!this.finder.items[item_idx].panels[panel_id]){
				this.$set(this.finder.items[item_idx].panels, panel_id, 'loading...');				
				var that = this;
				$.ajax({
					'url': this.finder.baseUrl,
					'data': {'finder_request':'detail', 'panel_id': panel_id}
				}).done(function(response){
					that.$set(that.finder.items[item_idx].panels, panel_id, response);
				});
			}
			this.current_panel = panel_id;
		}
	  },
	  data: {
	  	is_show_search_bar: false,
	    finder: {!! $finder->json() !!},
	    current_detail: undefined,
	    current_panel: 0,
	    current_tab: 0,
	    selected: []
	  }
	})
})
</script>

@yield('scripts')
@endsection