export function saveToken(token) {
    const expireTime = Date.now() + 15 * 60 * 1000; // 15 minutes en ms
    localStorage.setItem("access_token", token)
    localStorage.setItem("token_expire", expireTime)
  }

  export function saveRole(role) {
    const expireTime = Date.now() + 15 * 60 * 1000; // 15 minutes en ms
    localStorage.setItem("role", role)
    localStorage.setItem("role_expire", expireTime)
  }

 export function getToken() {
    const token = localStorage.getItem("access_token")
    const expireTime = localStorage.getItem("token_expire")
  
    if (!token || !expireTime) return null
  
    if (Date.now() > parseInt(expireTime)) {
      // token expiré
      localStorage.removeItem("access_token")
      localStorage.removeItem("token_expire")
      return null
    }
  
    return token
  }
  