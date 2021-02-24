
  <!DOCTYPE html>
  <html>
    <head>
      <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>BIS API</title>
      <style> 
        img {
            padding: 5px;
            width: 95px;
            height:75px;
          }
      </style>
    </head>

    <body>
       
            <!-- Dropdown Structure -->
          <ul id="dropdown1" class="dropdown-content">
            <li><a href="#!">one</a></li>
            <li><a href="#!">two</a></li>
            <li class="divider"></li>
            <li><a href="#!">three</a></li>
          </ul>
          
            <nav>
                <div class="nav-wrapper indigo lighten-1">
                <a href="#!" class="brand-logo" >
                  <img src="{{'/images/logo.png' }}" class="circle responsive-img">
                </a>
                <ul class="right hide-on-med-and-down">
                    <li class="active"><a href="#">BIS</a></li>
                    <li><a href="#">ODOO</a></li>
                    <li ><a href="#">Bridging</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="menubar"><i class="material-icons">more_vert</i></a></li> 
                    <!-- <li><a class="dropdown-trigger" href="#!" data-target="dropdown1">Dropdown<i class="material-icons right">arrow_drop_down</i></a></li> -->
                </ul>
              </div>
              </nav>
            
        
        <div class="row">
            <div class="col s12 ">
            <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                <span class="card-title">Under Construction</span>
                <p>Api Integratoin between BISMySql and Odoo</p>
                </div>
                <div class="card-action">
                <a href="#">lobotijo</a>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
          <div class="col s12">
          
            
              <div class="col s12 m8">
                <div class="card blue-grey lighten-5">
                  <div class="card-content ">
                    <span class="card-title"><h1>Update Batch Delivery Order</h1></span>
                    <p>Parameter : NO Delivery (DOBLG20200400128)</p>
                    <table>
                      <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                            <th>Note</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr>
                          <td>Delivery Number</td>
                          <td>DOBLG/200709/00001</td>
                          <td>Required</td>
                        </tr>
                        <tr>
                          <td>Wharehouse Code</td>
                          <td>GDG01</td>
                          <td>Required</td>
                        </tr>
                        <tr>
                          <td>Item Code</td>
                          <td>KFI-CDR-02</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>Batch</td>
                          <td>20080401</td>
                          <td>Required</td>
                        </tr>
                        <tr>
                          <td>Qty</td>
                          <td>2</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>Units</td>
                          <td>MASTER BOX</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>Expired</td>
                          <td>2008-04-01</td>
                          <td>Required</td>
                        </tr>
                      </tbody>
                  </table>
            
                  </div>
                  <div class="card-action">
                  <ul class="collapsible">
                    <li>
                      <div class="collapsible-header">
                        <i class="material-icons">filter_drama</i>
                        Success 200
                        <span class="new badge">4</span></div>
                      <div class="collapsible-body"><p>Lorem ipsum dolor sit amet.</p></div>
                    </li>
                    <li>
                      <div class="collapsible-header">
                        <i class="material-icons">place</i>
                        Failed 400
                        <span class="badge">1</span></div>
                      <div class="collapsible-body"><p>Lorem ipsum dolor sit amet.</p></div>
                    </li>
                  </ul>
                  </div>
                </div>
              </div>
            

            
              <div class="col s12 m4">
                <div class="card blue-grey darken-1">
                  <div class="card-content white-text">
                    <span class="card-title">Sample</span>
                    <p> End Point 197.168.21.175/delivery/DOBLG20200400128
                       <pre>
                          <code>
[
  {
    'no_delivery': "DOBLG/202004/00128",
    'kode_gudang': "GDG01",
    'kode_barang': "KFI-CDR-02",
    'no_batch'   : "20080401",
    'jumlah'     : "2",
    'satuan'     : "MASTER BOX",
    'kadaluarsa' : "2008-04-01",
    'terima'     : "2",
  }
]

                          </code>
                      </pre>
                    </p>
                  </div>
                  <div class="card-action">
                    
                  </div>
                </div>
              </div>
            

          
          </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>    
    </body>
  </html>
        