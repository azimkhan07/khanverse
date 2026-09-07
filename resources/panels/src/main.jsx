import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './theme/css/theme-variables.css'
import './theme/css/scrollbar-hide.css'
import './index.css'
import App from './App.jsx'

const saved = localStorage.getItem('skillnest-theme-public') || 'light';
document.documentElement.setAttribute('data-theme', saved);

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)
