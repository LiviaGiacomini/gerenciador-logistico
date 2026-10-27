import { useEffect, useMemo, useState, type FormEvent, type ReactNode } from 'react'

type Route = 'inicio' | 'produtos' | 'enderecos' | 'transportadores' | 'fretes' | 'relatorios'
type IconName = Route | 'menu' | 'search' | 'plus' | 'bell' | 'close' | 'box' | 'truck' | 'route' | 'check' | 'download' | 'edit' | 'trash' | 'arrow'

const nav: { id: Route; label: string }[] = [
  { id: 'inicio', label: 'Início' },
  { id: 'produtos', label: 'Produtos' },
  { id: 'enderecos', label: 'Endereços' },
  { id: 'transportadores', label: 'Transportadores' },
  { id: 'fretes', label: 'Fretes' },
  { id: 'relatorios', label: 'Relatórios' },
]

const products = [
  { code: 'PRD-001', name: 'Notebook Pro 14"', category: 'Eletrônicos', stock: 24, unit: 'un.', value: 'R$ 5.899,00', status: 'Disponível' },
  { code: 'PRD-002', name: 'Cadeira Ergonômica', category: 'Mobiliário', stock: 8, unit: 'un.', value: 'R$ 1.249,90', status: 'Estoque baixo' },
  { code: 'PRD-003', name: 'Monitor UltraWide 29"', category: 'Eletrônicos', stock: 17, unit: 'un.', value: 'R$ 1.899,00', status: 'Disponível' },
  { code: 'PRD-004', name: 'Kit Teclado e Mouse', category: 'Periféricos', stock: 42, unit: 'kit', value: 'R$ 289,90', status: 'Disponível' },
  { code: 'PRD-005', name: 'Impressora Laser', category: 'Eletrônicos', stock: 3, unit: 'un.', value: 'R$ 2.340,00', status: 'Estoque baixo' },
]

const carriers = [
  { name: 'TransLog Brasil', doc: '12.345.678/0001-90', contact: 'Carlos Mendes', phone: '(11) 98765-4321', region: 'Nacional', status: 'Ativo' },
  { name: 'Rota Sul Transportes', doc: '23.456.789/0001-01', contact: 'Marina Costa', phone: '(51) 99842-1100', region: 'Sul e Sudeste', status: 'Ativo' },
  { name: 'Norte Cargas', doc: '34.567.890/0001-12', contact: 'Paulo Reis', phone: '(91) 98823-7721', region: 'Norte', status: 'Inativo' },
  { name: 'Expresso Nordeste', doc: '45.678.901/0001-23', contact: 'Ana Lima', phone: '(81) 99731-5502', region: 'Nordeste', status: 'Ativo' },
]

const freights = [
  { id: '#FRT-1048', route: 'São Paulo, SP → Curitiba, PR', carrier: 'TransLog Brasil', date: '18 Jun 2025', value: 'R$ 1.840,00', status: 'Em trânsito' },
  { id: '#FRT-1047', route: 'Porto Alegre, RS → Florianópolis, SC', carrier: 'Rota Sul Transportes', date: '17 Jun 2025', value: 'R$ 980,00', status: 'Entregue' },
  { id: '#FRT-1046', route: 'Recife, PE → Salvador, BA', carrier: 'Expresso Nordeste', date: '16 Jun 2025', value: 'R$ 1.260,00', status: 'Pendente' },
  { id: '#FRT-1045', route: 'Campinas, SP → Belo Horizonte, MG', carrier: 'TransLog Brasil', date: '14 Jun 2025', value: 'R$ 1.420,00', status: 'Entregue' },
  { id: '#FRT-1044', route: 'Belém, PA → Manaus, AM', carrier: 'Norte Cargas', date: '12 Jun 2025', value: 'R$ 2.780,00', status: 'Cancelado' },
]

const addresses = [
  { name: 'Centro de Distribuição SP', type: 'Armazém principal', address: 'Av. das Nações, 1240 — São Paulo, SP', cep: '04578-000', capacity: '3.200 m²' },
  { name: 'Unidade Curitiba', type: 'Ponto de apoio', address: 'Rua Industrial, 890 — Curitiba, PR', cep: '81200-240', capacity: '1.450 m²' },
  { name: 'Hub Recife', type: 'Centro regional', address: 'Av. Recife, 3500 — Recife, PE', cep: '50870-000', capacity: '1.980 m²' },
  { name: 'Depósito Porto Alegre', type: 'Armazém', address: 'Rua dos Andradas, 620 — Porto Alegre, RS', cep: '90020-004', capacity: '1.120 m²' },
]

function Icon({ name, size = 20 }: { name: IconName; size?: number }) {
  const paths: Record<IconName, ReactNode> = {
    inicio: <><path d="M3 11 12 3l9 8"/><path d="M5 10v10h14V10M9 20v-6h6v6"/></>,
    produtos: <><path d="m4 7 8-4 8 4-8 4-8-4Z"/><path d="M4 7v10l8 4 8-4V7M12 11v10"/></>,
    enderecos: <><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></>,
    transportadores: <><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></>,
    fretes: <><circle cx="6" cy="18" r="2"/><circle cx="18" cy="6" r="2"/><path d="M7.5 16.5 16.5 7.5M6 6h5M6 6v5"/></>,
    relatorios: <><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></>,
    menu: <path d="M4 7h16M4 12h16M4 17h16"/>,
    search: <><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></>,
    plus: <path d="M12 5v14M5 12h14"/>,
    bell: <><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></>,
    close: <path d="m6 6 12 12M18 6 6 18"/>,
    box: <><path d="m4 7 8-4 8 4-8 4-8-4Z"/><path d="M4 7v10l8 4 8-4V7"/></>,
    truck: <><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></>,
    route: <><circle cx="5" cy="19" r="2"/><circle cx="19" cy="5" r="2"/><path d="M6.5 17.5 17.5 6.5"/></>,
    check: <path d="m5 12 4 4L19 6"/>,
    download: <><path d="M12 3v12m-5-5 5 5 5-5"/><path d="M4 21h16"/></>,
    edit: <><path d="m4 16-1 5 5-1L19 9l-4-4L4 16Z"/><path d="m13 7 4 4"/></>,
    trash: <><path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14"/></>,
    arrow: <path d="m9 18 6-6-6-6"/>,
  }
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>
}

function Badge({ children }: { children: string }) {
  const tone = children === 'Entregue' || children === 'Disponível' || children === 'Ativo' ? 'green' : children === 'Cancelado' || children === 'Inativo' ? 'red' : children === 'Em trânsito' ? 'blue' : 'orange'
  return <span className={`badge ${tone}`}><span className="badge-dot" />{children}</span>
}

function EmptyFriendlyTable({ columns, children }: { columns: string[]; children: ReactNode }) {
  return <div className="table-wrap"><table><thead><tr>{columns.map(c => <th key={c}>{c}</th>)}</tr></thead><tbody>{children}</tbody></table></div>
}

function PageHeader({ eyebrow, title, description, action, onAction }: { eyebrow?: string; title: string; description: string; action?: string; onAction?: () => void }) {
  return <div className="page-heading">
    <div>{eyebrow && <span className="eyebrow">{eyebrow}</span>}<h1>{title}</h1><p>{description}</p></div>
    {action && <button className="primary-button" onClick={onAction}><Icon name="plus" size={18} />{action}</button>}
  </div>
}

function SearchBar({ value, onChange, placeholder = 'Buscar...' }: { value: string; onChange: (v: string) => void; placeholder?: string }) {
  return <label className="search"><Icon name="search" size={18} /><input value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder} /></label>
}

function Dashboard({ go }: { go: (r: Route) => void }) {
  const cards = [
    { label: 'Total de Produtos', value: '148', note: '+12 este mês', icon: 'box' as IconName, tone: 'blue' },
    { label: 'Transportadores', value: '24', note: '21 ativos', icon: 'truck' as IconName, tone: 'purple' },
    { label: 'Fretes em Andamento', value: '16', note: '6 chegam hoje', icon: 'route' as IconName, tone: 'orange' },
    { label: 'Entregas Concluídas', value: '93', note: '+8,4% no período', icon: 'check' as IconName, tone: 'green' },
  ]
  return <>
    <PageHeader eyebrow="VISÃO GERAL" title="Painel de Controle" description="Acompanhe os principais indicadores da sua operação logística." />
    <section className="stat-grid">{cards.map(c => <article className="stat-card" key={c.label}>
      <div className={`stat-icon ${c.tone}`}><Icon name={c.icon} size={22}/></div>
      <div className="stat-value">{c.value}</div><div className="stat-label">{c.label}</div><div className={`stat-note ${c.tone}`}>{c.note}</div>
    </article>)}</section>
    <section className="dashboard-grid">
      <article className="card chart-card">
        <div className="card-title"><div><h2>Movimentação de fretes</h2><p>Últimos 7 meses</p></div><select aria-label="Período"><option>Este ano</option><option>Últimos 6 meses</option></select></div>
        <div className="chart">
          <div className="y-labels"><span>40</span><span>30</span><span>20</span><span>10</span><span>0</span></div>
          <div className="bars">{[18,24,21,30,27,36,32].map((v,i) => <div className="bar-col" key={i}><div className="bar" style={{height: `${v * 2.1}%`}}><span>{v}</span></div><small>{['Dez','Jan','Fev','Mar','Abr','Mai','Jun'][i]}</small></div>)}</div>
        </div>
      </article>
      <article className="card">
        <div className="card-title"><div><h2>Status dos fretes</h2><p>Distribuição atual</p></div></div>
        <div className="donut-row"><div className="donut"><span>48<small>fretes</small></span></div>
          <div className="legend"><span><i className="dot blue-bg"/>Em trânsito <b>16</b></span><span><i className="dot green-bg"/>Entregues <b>21</b></span><span><i className="dot orange-bg"/>Pendentes <b>8</b></span><span><i className="dot gray-bg"/>Cancelados <b>3</b></span></div>
        </div>
      </article>
    </section>
    <section className="card recent">
      <div className="card-title"><div><h2>Fretes recentes</h2><p>Últimas movimentações cadastradas</p></div><button className="text-button" onClick={() => go('fretes')}>Ver todos <Icon name="arrow" size={16}/></button></div>
      <EmptyFriendlyTable columns={['Código', 'Rota', 'Transportador', 'Data', 'Valor', 'Status']}>
        {freights.slice(0,4).map(f => <tr key={f.id}><td className="strong">{f.id}</td><td>{f.route}</td><td>{f.carrier}</td><td>{f.date}</td><td className="strong">{f.value}</td><td><Badge>{f.status}</Badge></td></tr>)}
      </EmptyFriendlyTable>
    </section>
  </>
}

function Products({ onNew }: { onNew: () => void }) {
  const [search, setSearch] = useState('')
  const list = products.filter(p => `${p.name} ${p.code} ${p.category}`.toLowerCase().includes(search.toLowerCase()))
  return <>
    <PageHeader eyebrow="CATÁLOGO" title="Produtos" description="Gerencie o catálogo e acompanhe os níveis de estoque." action="Novo produto" onAction={onNew}/>
    <div className="card list-card"><div className="toolbar"><SearchBar value={search} onChange={setSearch} placeholder="Buscar por nome, código ou categoria..." /><select><option>Todas as categorias</option><option>Eletrônicos</option><option>Mobiliário</option></select></div>
      <EmptyFriendlyTable columns={['Código','Produto','Categoria','Estoque','Valor unitário','Status','Ações']}>
        {list.map(p => <tr key={p.code}><td className="code">{p.code}</td><td className="strong">{p.name}</td><td>{p.category}</td><td><b>{p.stock}</b> {p.unit}</td><td>{p.value}</td><td><Badge>{p.status}</Badge></td><td><Actions/></td></tr>)}
      </EmptyFriendlyTable><div className="table-footer">Exibindo {list.length} de 148 produtos <span>Página <b>1</b> de 30</span></div>
    </div>
  </>
}

function Actions() { return <div className="actions"><button title="Editar"><Icon name="edit" size={17}/></button><button title="Excluir" className="danger"><Icon name="trash" size={17}/></button></div> }

function Addresses({ onNew }: { onNew: () => void }) {
  return <><PageHeader eyebrow="LOCALIZAÇÕES" title="Endereços" description="Organize armazéns, centros de distribuição e pontos de apoio." action="Novo endereço" onAction={onNew}/>
    <div className="address-grid">{addresses.map((a,i) => <article className="card address-card" key={a.name}><div className="address-top"><div className="location-icon"><Icon name="enderecos"/></div><Badge>{i === 0 ? 'Ativo' : 'Disponível'}</Badge></div><h2>{a.name}</h2><span className="muted">{a.type}</span><p>{a.address}</p><div className="address-meta"><span><small>CEP</small><b>{a.cep}</b></span><span><small>Capacidade</small><b>{a.capacity}</b></span></div><button className="outline-button"><Icon name="edit" size={16}/> Editar endereço</button></article>)}</div>
  </>
}

function Carriers({ onNew }: { onNew: () => void }) {
  const [search,setSearch] = useState('')
  const list = carriers.filter(c => c.name.toLowerCase().includes(search.toLowerCase()))
  return <><PageHeader eyebrow="PARCEIROS" title="Transportadores" description="Mantenha os dados dos parceiros de transporte atualizados." action="Novo transportador" onAction={onNew}/>
    <div className="card list-card"><div className="toolbar"><SearchBar value={search} onChange={setSearch} placeholder="Buscar transportador..." /><select><option>Todos os status</option><option>Ativos</option><option>Inativos</option></select></div>
    <EmptyFriendlyTable columns={['Empresa','CNPJ','Contato','Telefone','Região atendida','Status','Ações']}>{list.map(c=><tr key={c.doc}><td className="strong">{c.name}</td><td className="code">{c.doc}</td><td>{c.contact}</td><td>{c.phone}</td><td>{c.region}</td><td><Badge>{c.status}</Badge></td><td><Actions/></td></tr>)}</EmptyFriendlyTable></div>
  </>
}

function Freights({ onNew }: { onNew: () => void }) {
  const [search,setSearch] = useState('')
  const list=freights.filter(f=>`${f.id} ${f.route} ${f.carrier}`.toLowerCase().includes(search.toLowerCase()))
  return <><PageHeader eyebrow="OPERAÇÃO" title="Fretes" description="Acompanhe coletas, rotas e entregas em um só lugar." action="Novo frete" onAction={onNew}/>
    <div className="mini-stats"><div><span>Total no mês</span><b>48</b></div><div><span>Em trânsito</span><b>16</b></div><div><span>Valor movimentado</span><b>R$ 74.820</b></div></div>
    <div className="card list-card"><div className="toolbar"><SearchBar value={search} onChange={setSearch} placeholder="Buscar código, rota ou transportador..." /><select><option>Todos os status</option><option>Em trânsito</option><option>Entregue</option><option>Pendente</option></select></div>
      <EmptyFriendlyTable columns={['Código','Origem e destino','Transportador','Previsão','Valor','Status','Ações']}>{list.map(f=><tr key={f.id}><td className="strong">{f.id}</td><td>{f.route}</td><td>{f.carrier}</td><td>{f.date}</td><td className="strong">{f.value}</td><td><Badge>{f.status}</Badge></td><td><Actions/></td></tr>)}</EmptyFriendlyTable>
    </div>
  </>
}

function Reports() {
  const [notice,setNotice] = useState(false)
  const exportFile=()=>{setNotice(true); setTimeout(()=>setNotice(false),2500)}
  return <><PageHeader eyebrow="ANÁLISES" title="Relatórios" description="Visualize resultados e exporte dados para apoiar suas decisões."/>
    <div className="report-filters card"><label>Período<input type="date" defaultValue="2025-06-01"/></label><label>Até<input type="date" defaultValue="2025-06-30"/></label><label>Tipo de relatório<select><option>Resumo operacional</option><option>Custos de frete</option><option>Estoque</option></select></label><button className="primary-button" onClick={exportFile}><Icon name="download" size={18}/>Exportar relatório</button></div>
    <div className="report-grid">
      {[['Relatório de produtos','Posição atual do estoque, categorias e itens com nível crítico.','148 itens'],['Desempenho de transportadores','Prazos, entregas concluídas e ocorrências por parceiro.','96% no prazo'],['Custos logísticos','Custos por rota, transportador e período selecionado.','R$ 74.820'],['Histórico de fretes','Relação completa das movimentações e seus respectivos status.','48 registros']].map((r,i)=><article className="card report-card" key={r[0]}><div className={`report-icon r${i}`}><Icon name={i===0?'box':i===1?'truck':i===2?'relatorios':'route'}/></div><div><h2>{r[0]}</h2><p>{r[1]}</p><b>{r[2]}</b></div><button onClick={exportFile}><Icon name="download" size={18}/></button></article>)}
    </div>
    <article className="card insight"><span>RESUMO DO PERÍODO</span><h2>Junho apresenta crescimento de 8,4% na operação</h2><p>O prazo médio de entrega caiu de 4,2 para 3,7 dias, enquanto 96% dos fretes foram concluídos dentro do prazo previsto.</p><div className="progress"><i/></div></article>
    {notice && <div className="toast"><Icon name="check" size={18}/> Relatório preparado para exportação.</div>}
  </>
}

function Modal({ type, onClose }: { type: Route; onClose: () => void }) {
  const titles: Partial<Record<Route,string>>={produtos:'Cadastrar produto',enderecos:'Cadastrar endereço',transportadores:'Cadastrar transportador',fretes:'Cadastrar frete'}
  const submit=(e:FormEvent)=>{e.preventDefault();onClose()}
  return <div className="modal-backdrop" onMouseDown={e=>e.target===e.currentTarget&&onClose()}><form className="modal" onSubmit={submit}><div className="modal-head"><div><span className="eyebrow">NOVO REGISTRO</span><h2>{titles[type]}</h2></div><button type="button" onClick={onClose}><Icon name="close"/></button></div>
    <div className="form-grid">
      <label>Nome / identificação<input required placeholder="Digite a identificação"/></label>
      <label>Categoria / tipo<select><option>Selecione uma opção</option><option>Eletrônicos</option><option>Operacional</option><option>Regional</option></select></label>
      <label className="full">Descrição / endereço<input required placeholder="Informe os detalhes do registro"/></label>
      <label>Quantidade / capacidade<input type="number" min="0" placeholder="0"/></label>
      <label>Status<select><option>Ativo</option><option>Pendente</option><option>Inativo</option></select></label>
    </div><div className="modal-actions"><button type="button" className="outline-button" onClick={onClose}>Cancelar</button><button className="primary-button" type="submit">Salvar cadastro</button></div>
  </form></div>
}

export default function App() {
  const initial = (location.hash.replace('#/','') || 'inicio') as Route
  const [route,setRoute] = useState<Route>(nav.some(n=>n.id===initial)?initial:'inicio')
  const [open,setOpen] = useState(false)
  const [modal,setModal] = useState<Route|null>(null)
  const go=(r:Route)=>{setRoute(r);location.hash=`/${r}`;setOpen(false);window.scrollTo({top:0,behavior:'smooth'})}
  useEffect(()=>{const change=()=>{const r=location.hash.replace('#/','') as Route;if(nav.some(n=>n.id===r))setRoute(r)};addEventListener('hashchange',change);return()=>removeEventListener('hashchange',change)},[])
  const page=useMemo(()=>nav.find(n=>n.id===route)?.label||'Início',[route])
  return <div className="app">
    <aside className={open?'sidebar open':'sidebar'}>
      <div className="brand"><div className="brand-mark"><Icon name="route" size={24}/></div><div><b>Gerenciador</b><span>LOGÍSTICO</span></div><button className="close-mobile" onClick={()=>setOpen(false)}><Icon name="close"/></button></div>
      <nav><span className="nav-label">MENU PRINCIPAL</span>{nav.map(n=><button key={n.id} className={route===n.id?'active':''} onClick={()=>go(n.id)}><Icon name={n.id}/><span>{n.label}</span>{route===n.id&&<i/>}</button>)}</nav>
      <div className="sidebar-footer"><div className="help-icon">?</div><div><b>Precisa de ajuda?</b><span>Consulte o guia do sistema</span></div></div>
    </aside>
    {open&&<div className="nav-backdrop" onClick={()=>setOpen(false)}/>}
    <div className="main">
      <header><button className="menu-button" onClick={()=>setOpen(true)}><Icon name="menu"/></button><div><span>Gerenciador Logístico</span><b>{page}</b></div><div className="header-right"><button className="notification"><Icon name="bell"/><i/></button><div className="avatar">AS</div><div className="user"><b>Alex Silva</b><span>Administrador</span></div></div></header>
      <main>
        {route==='inicio'&&<Dashboard go={go}/>}
        {route==='produtos'&&<Products onNew={()=>setModal(route)}/>}
        {route==='enderecos'&&<Addresses onNew={()=>setModal(route)}/>}
        {route==='transportadores'&&<Carriers onNew={()=>setModal(route)}/>}
        {route==='fretes'&&<Freights onNew={()=>setModal(route)}/>}
        {route==='relatorios'&&<Reports/>}
        <footer>Gerenciador Logístico · Projeto acadêmico de Fundamentos de Programação para Internet</footer>
      </main>
    </div>
    {modal&&<Modal type={modal} onClose={()=>setModal(null)}/>}
  </div>
}
