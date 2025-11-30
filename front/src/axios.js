import axios from "axios";
import {getToken} from "@/utils"
const instance = axios.create({
  baseURL: 'http://localhost:8000/api',
  //timeout: 10000
});
instance.interceptors.request.use(config => {
  const token = getToken('token');
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }
  return config;
}, error => {
  return Promise.reject(error);
});

export default instance;

