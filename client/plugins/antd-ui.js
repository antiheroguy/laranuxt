import Vue from 'vue'

import {
  Breadcrumb,
  Button,
  Card,
  Checkbox,
  Col,
  Collapse,
  ConfigProvider,
  Dropdown,
  FormModel,
  Icon,
  Input,
  Layout,
  Menu,
  Modal,
  Row,
  Result,
  Select,
  Spin,
  Table,
  Tree,
  notification
} from 'ant-design-vue'

Vue.use(Breadcrumb)
Vue.use(Button)
Vue.use(Card)
Vue.use(Checkbox)
Vue.use(Col)
Vue.use(Collapse)
Vue.use(ConfigProvider)
Vue.use(Dropdown)
Vue.use(FormModel)
Vue.use(Icon)
Vue.use(Input)
Vue.use(Layout)
Vue.use(Menu)
Vue.use(Modal)
Vue.use(Row)
Vue.use(Result)
Vue.use(Select)
Vue.use(Spin)
Vue.use(Table)
Vue.use(Tree)

Vue.prototype.$notification = notification
Vue.prototype.$confirm = Modal.confirm
